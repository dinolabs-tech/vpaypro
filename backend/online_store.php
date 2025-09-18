<?php
// Ensure db_connection is included first
require_once 'database/db_connection.php';

// Debug: Verify models/online_store.php exists
if (!file_exists('models/online_store.php')) {
    die('Error: models/online_store.php file not found');
}
require_once 'models/online_store.php';
require_once 'models/tax_rates.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['user_id']) || !isset($_SESSION['customer_id'])) {
    header("Location: index.php");
    exit();
}

// Initialize models
$onlineStore = new OnlineStore($conn);
$taxRateModel = new TaxRate($conn);

// Fetch delivery fee
$sql = "SELECT setting_value FROM settings WHERE setting_key = 'delivery_fee'";
$result = $conn->query($sql);
$delivery_fee = ($result && $result->num_rows > 0) ? (float)$result->fetch_assoc()['setting_value'] : 500;

// Initialize variables
$message = '';
$cartItems = $onlineStore->getCartItems($_SESSION['customer_id']);
$cartTotal = 0;
foreach ($cartItems as $item) {
    $cartTotal += $item['price'] * $item['quantity'];
}
$countryForTax = $_SESSION['country'] ?? null;
$stateForTax = $_SESSION['state'] ?? null;
$taxRate = $taxRateModel->getTaxRateByLocation($countryForTax, $stateForTax);
$taxAmount = $cartTotal * ($taxRate / 100);
$totalWithTax = $cartTotal + $taxAmount;
$customer_balance = $onlineStore->getCustomerBalance($_SESSION['user_id']);

// Fetch products for display
$products = $onlineStore->getProducts($_SESSION['country'] ?? null, $_SESSION['state'] ?? null);

// Handle checkout action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    if (empty($_SESSION['online_cart'])) {
        $message = "Cart is empty. Add products before checkout.";
    } else {
        $cartTotal = 0;
        foreach ($_SESSION['online_cart'] as $item) {
            $itemTotal = $item['price'] * $item['qty'];
            $cartTotal += $itemTotal;
        }

        $total_amount_with_delivery = $cartTotal + $delivery_fee;

        // Check customer status and balance
        $stmt = $conn->prepare("SELECT balance, account_status, customer_id FROM customers WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $customer = $result->fetch_assoc();
        $stmt->close();

        if ($customer['account_status'] === 'disabled') {
            $message = "Your account is disabled. You cannot place orders.";
        } elseif ($customer['balance'] < $total_amount_with_delivery) {
            $message = "Insufficient balance to complete the purchase.";
        } else {
            // Process order
            $delivery_address = filter_input(INPUT_POST, 'delivery_address', FILTER_SANITIZE_STRING);
            $selected_country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
            $selected_state = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_STRING);

            $taxRate = $taxRateModel->getTaxRateByLocation($selected_country, $selected_state);
            $taxAmount = ($cartTotal * $taxRate) / 100;
            $totalWithTax = $cartTotal + $taxAmount;
            $final_total_amount = $totalWithTax + $delivery_fee;

            // Insert into orders table
            $stmt = $conn->prepare("INSERT INTO orders (customer_id, total_amount, delivery_address, delivery_fee, country, state) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sdsdss", $customer['customer_id'], $final_total_amount, $delivery_address, $delivery_fee, $selected_country, $selected_state);
            $stmt->execute();
            $order_id = $stmt->insert_id;
            $stmt->close();

            // Insert into order_items table and deduct inventory
            $stmt_order_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt_inventory = $conn->prepare("UPDATE branch_product_inventory SET quantity = quantity - ? WHERE productid = ? AND branch_id = ?");

            foreach ($_SESSION['online_cart'] as $item) {
                $stmt_order_item->bind_param("iiid", $order_id, $item['product_id'], $item['qty'], $item['price']);
                $stmt_order_item->execute();

                $stmt_inventory->bind_param("iii", $item['qty'], $item['product_id'], $item['branch_id']);
                $stmt_inventory->execute();
            }
            $stmt_order_item->close();
            $stmt_inventory->close();

            // Deduct from balance
            $new_balance = $customer['balance'] - $final_total_amount;
            $stmt = $conn->prepare("UPDATE customers SET balance = ? WHERE id = ?");
            $stmt->bind_param("di", $new_balance, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->close();

            // Record customer transaction
            $stmt = $conn->prepare("INSERT INTO customer_transactions (customer_id, transaction_type, amount) VALUES (?, 'purchase', ?)");
            $stmt->bind_param("sd", $customer['customer_id'], $final_total_amount);
            $stmt->execute();
            $stmt->close();

            // Create delivery record
            $stmt = $conn->prepare("INSERT INTO deliveries (order_id, country, state) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $order_id, $selected_country, $selected_state);
            $stmt->execute();
            $stmt->close();

            // Send email notification
            $stmt = $conn->prepare("SELECT email, name FROM customers WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $customer_data = $result->fetch_assoc();
            $stmt->close();

            if ($customer_data && !empty($customer_data['email'])) {
                require_once __DIR__ . '/phpmailer/src/Exception.php';
                require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
                require_once __DIR__ . '/phpmailer/src/SMTP.php';

                $customer_email = $customer_data['email'];
                $customer_name_for_email = trim($customer_data['name']);
                $invoice_html = "<h3>Order Summary (ID: $order_id)</h3><table border='1' cellpadding='5' cellspacing='0' width='100%'><tr><th>Product</th><th>Quantity</th><th>Price</th><th>Total</th></tr>";
                $emailCartTotal = 0;
                foreach ($_SESSION['online_cart'] as $item) {
                    $itemTotal = $item['price'] * $item['qty'];
                    $emailCartTotal += $itemTotal;
                    $invoice_html .= "<tr><td>" . htmlspecialchars($item['product_name']) . "</td><td>" . $item['qty'] . "</td><td>₦" . number_format($item['price'], 2) . "</td><td>₦" . number_format($itemTotal, 2) . "</td></tr>";
                }
                $invoice_html .= "<tr><td colspan='3' align='right'><strong>Subtotal:</strong></td><td>₦" . number_format($emailCartTotal, 2) . "</td></tr>";
                $invoice_html .= "<tr><td colspan='3' align='right'><strong>Delivery Fee:</strong></td><td>₦" . number_format($delivery_fee, 2) . "</td></tr>";
                $invoice_html .= "<tr><td colspan='3' align='right'><strong>Tax (" . number_format($taxRate, 2) . "%):</strong></td><td>₦" . number_format($taxAmount, 2) . "</td></tr>";
                $invoice_html .= "<tr><td colspan='3' align='right'><strong>Total:</strong></td><td>₦" . number_format($final_total_amount, 2) . "</td></tr>";
                $invoice_html .= "</table>";

                try {
                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = 'mail.dinolabstech.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'enquiries@dinolabstech.com';
                    $mail->Password = 'Dinolabs@11';
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->setFrom('enquiries@dinolabstech.com', 'Dinolabs Tech Services');
                    $mail->addAddress($customer_email, $customer_name_for_email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Your Online Store Order Confirmation - Order #' . $order_id;
                    $mail->Body = "
                        <p>Dear $customer_name_for_email,</p>
                        <p>Thank you for your order from our online store. We have received it and are preparing it for shipment.</p>
                        <p><strong>Delivery Address:</strong> " . htmlspecialchars($delivery_address) . "</p>
                        $invoice_html
                        <p>We appreciate your business!</p>
                        <p>Best regards,<br>The Dinolabs Team</p>
                    ";
                    $mail->send();
                } catch (Exception $e) {
                    error_log("PHPMailer Error: " . $mail->ErrorInfo);
                }
            }

            unset($_SESSION['online_cart']);
            $message = "Order placed successfully! Your order ID is " . $order_id;
        }
    }

    // Recalculate cart totals after checkout
    $cartItems = $onlineStore->getCartItems($_SESSION['customer_id']);
    $cartTotal = 0;
    foreach ($cartItems as $item) {
        $cartTotal += $item['price'] * $item['quantity'];
    }
    $taxRate = $taxRateModel->getTaxRateByLocation($countryForTax, $stateForTax);
    $taxAmount = $cartTotal * ($taxRate / 100);
    $totalWithTax = $cartTotal + $taxAmount;
    $customer_balance = $onlineStore->getCustomerBalance($_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include('components/head.php'); ?>

<body>
    <div class="wrapper">
        <?php include('components/customer_sidebar.php'); ?>

        <div class="main-panel">
            <?php include('components/navbar.php'); ?>

            <div class="container">
                <div class="page-inner">
                    <div>
                        <h3 class="fw-bold mb-3">Online Store</h3>
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="customer_dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Online Store</li>
                            </ol>
                        </nav>
                    </div>

                    <?php if ($message): ?>
                        <div class="alert alert-info">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <section class="section">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Product List</h4>
                                    </div>
                                    <div class="card-body">
                                        <form id="branchSelectorForm" class="mt-3">
                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-3">
                                                    <select class="form-select" id="country_product_list" name="country_product_list" required>
                                                        <option value="">Select Country</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <select class="form-select" id="state_product_list" name="state_product_list" required>
                                                        <option value="">Select State</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <select class="form-select" id="branch" name="branch" required>
                                                        <option value="">Select Branch</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </form>
                                        <div id="product-cards-container" class="row pt-5">
                                            <!-- Products will be loaded here via JavaScript -->
                                        </div>
                                        <div id="pagination-container" class="mt-4"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card mt-4" id="cart-container">
                                    <?php include('components/cart_display.php'); ?>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Checkout Modal -->
                    <div class="modal fade" id="checkoutModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Checkout</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" id="checkoutForm">
                                        <input type="hidden" name="action" value="checkout">
                                        <div class="mb-3">
                                            <select class="form-select" id="country" name="country" required>
                                                <option value="">Select Country</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <select class="form-select" id="state" name="state" required>
                                                <option value="">Select State</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="delivery_address" class="form-label text-black">Delivery Address</label>
                                            <textarea class="form-control" id="delivery_address" name="delivery_address" rows="3" required></textarea>
                                        </div>

                                        <p>Subtotal: <strong>₦<?= number_format($cartTotal, 2) ?></strong></p>
                                        <p>Delivery Fee: <strong>₦<?= number_format($delivery_fee, 2) ?></strong></p>
                                        <p>Tax (<?= number_format($taxRate, 2) ?>%): <strong>₦<?= number_format($taxAmount, 2) ?></strong></p>
                                        <p>Total Amount: <strong>₦<?= number_format($totalWithTax + $delivery_fee, 2) ?></strong></p>
                                        <p>Your current balance is: <strong>₦<?= number_format($customer_balance, 2) ?></strong></p>
                                        <?php if ($customer_balance < ($totalWithTax + $delivery_fee) && $totalWithTax > 0): ?>
                                            <div class="alert alert-danger">
                                                Your balance is insufficient for this purchase. Please top up your account.
                                            </div>
                                        <?php endif; ?>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" form="checkoutForm" class="btn btn-primary" <?php if ($customer_balance < ($totalWithTax + $delivery_fee) && $totalWithTax > 0) echo 'disabled'; ?>>Place Order</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('components/footer.php'); ?>
            <script src="assets/js/country_state_selector.js"></script>
        </div>
    </div>
    <?php include('components/script.php'); ?>
    <script>
        $(document).ready(function() {
            // Fetch initial cart and balance data on page load
            fetchInitialData();

            // Function to fetch initial cart and balance data
            function fetchInitialData() {
                $.ajax({
                    url: 'ajax/get_cart_and_balance.php',
                    type: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Update hidden fields
                            $('#hiddenCartTotal').val(response.cartTotal);
                            $('#hiddenTaxRate').val(response.taxRate);
                            $('#hiddenTaxAmount').val(response.taxAmount);
                            $('#hiddenTotalWithTax').val(response.totalWithTax);
                            $('#hiddenDeliveryFee').val(response.deliveryFee);
                            $('#hiddenCustomerBalance').val(response.customerBalance);
                            // Update cart display
                            updateCartDisplay(response);
                            // Update checkout modal
                            updateCheckoutModal();
                        } else {
                            console.error('Error fetching initial data:', response.message);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX Error fetching initial data:', textStatus, errorThrown);
                    }
                });
            }
        });

        // Helper function to escape HTML for security
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, function(m) {
                return map[m];
            });
        }

        // Fetch Countries
        $.ajax({
            url: 'ajax/get_locations.php',
            type: 'GET',
            success: function(data) {
                var options = '<option value="">Select Country</option>';
                data.forEach(function(country) {
                    options += '<option value="' + escapeHtml(country.id) + '">' + escapeHtml(country.name) + '</option>';
                });
                $('#country_product_list').html(options);
                $('#country').html(options);
            }
        });

        // Fetch States on Country Change
        $('#country_product_list').change(function() {
            var countryId = $(this).val();
            if (countryId) {
                $.ajax({
                    url: 'ajax/get_locations.php',
                    type: 'GET',
                    data: {
                        country_id: countryId
                    },
                    success: function(data) {
                        var options = '<option value="">Select State</option>';
                        data.forEach(function(state) {
                            options += '<option value="' + escapeHtml(state.id) + '">' + escapeHtml(state.name) + '</option>';
                        });
                        $('#state_product_list').html(options);
                        $('#state').html(options);
                        $('#branch').html('<option value="">Select Branch</option>');
                        $('#product-cards-container').html('');
                    }
                });
            } else {
                $('#state_product_list').html('<option value="">Select State</option>');
                $('#state').html('<option value="">Select State</option>');
                $('#branch').html('<option value="">Select Branch</option>');
                $('#product-cards-container').html('');
            }
        });

        // Fetch Branches on State Change
        $('#state_product_list').change(function() {
            var stateId = $(this).val();
            if (stateId) {
                $.ajax({
                    url: 'ajax/get_locations.php',
                    type: 'GET',
                    data: {
                        state_id: stateId
                    },
                    success: function(data) {
                        var options = '<option value="">Select Branch</option>';
                        data.forEach(function(branch) {
                            options += '<option value="' + escapeHtml(branch.id) + '">' + escapeHtml(branch.name) + '</option>';
                        });
                        $('#branch').html(options);
                        $('#product-cards-container').html('');
                    }
                });
            } else {
                $('#branch').html('<option value="">Select Branch</option>');
                $('#product-cards-container').html('');
            }
        });

        // Function to fetch products and display them as cards
        function fetchProducts(branchId, page = 1) {
            $('#product-cards-container').html('<div class="col-12 text-center">Loading products...</div>');
            $('#pagination-container').html('');

            $.ajax({
                url: 'ajax/get_products_by_branch.php',
                type: 'GET',
                data: {
                    branch_id: branchId,
                    page: page
                },
                success: function(response) {
                    var productCardsHtml = '';
                    if (response.status === 'success' && response.products.length > 0) {
                        response.products.forEach(function(product) {
                            var imageUrl = product.image_url ? product.image_url : 'assets/img/default.jpg';
                            productCardsHtml += '<div class="col-md-4 mb-4">';
                            productCardsHtml += '  <div class="card h-100">';
                            productCardsHtml += '    <img src="' + escapeHtml(imageUrl) + '" class="card-img-top" alt="' + escapeHtml(product.productname) + '" style="height: 200px; object-fit: cover;">';
                            productCardsHtml += '    <div class="card-body d-flex flex-column">';
                            productCardsHtml += '      <h5 class="card-title">' + escapeHtml(product.productname) + '</h5>';
                            productCardsHtml += '      <p class="card-text"><strong>Price: ₦' + parseFloat(product.sellprice).toFixed(2) + '</strong></p>';
                            productCardsHtml += '      <div class="mt-auto">';
                            productCardsHtml += '        <form method="POST" class="add-to-cart-form">';
                            productCardsHtml += '          <input type="hidden" name="action" value="add_to_cart">';
                            productCardsHtml += '          <input type="hidden" name="product_id" value="' + escapeHtml(product.productid) + '">';
                            productCardsHtml += '          <input type="hidden" name="product_name" value="' + escapeHtml(product.productname) + '">';
                            productCardsHtml += '          <input type="hidden" name="price" value="' + escapeHtml(product.sellprice) + '">';
                            productCardsHtml += '          <input type="hidden" name="branch_id" value="' + escapeHtml(branchId) + '">';
                            productCardsHtml += '          <div class="input-group mb-2">';
                            productCardsHtml += '            <input type="number" name="qty" value="1" min="1" max="' + parseInt(product.quantity) + '" class="form-control" style="width: 70px;">';
                            productCardsHtml += '            <button type="submit" class="btn btn-success">';
                            productCardsHtml += '              <i class="bi bi-cart-plus"></i> Add';
                            productCardsHtml += '            </button>';
                            productCardsHtml += '          </div>';
                            productCardsHtml += '        </form>';
                            productCardsHtml += '      </div>';
                            productCardsHtml += '    </div>';
                            productCardsHtml += '  </div>';
                            productCardsHtml += '</div>';
                        });
                    } else {
                        productCardsHtml = '<div class="col-12 text-center">' + escapeHtml(response.message) + '</div>';
                    }

                    $('#product-cards-container').html(productCardsHtml);
                    renderPagination(response.total, response.page, branchId);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error: ", textStatus, errorThrown);
                    $('#product-cards-container').html('<div class="col-12 text-center">Error loading products. Please try again.</div>');
                }
            });
        }

        // Function to update the cart display
        function updateCartDisplay(response) {
            if (response && response.html) {
                $('#cart-container').html(response.html);
                // Update hidden fields if provided
                if (response.cartTotal) $('#hiddenCartTotal').val(response.cartTotal);
                if (response.taxRate) $('#hiddenTaxRate').val(response.taxRate);
                if (response.taxAmount) $('#hiddenTaxAmount').val(response.taxAmount);
                if (response.totalWithTax) $('#hiddenTotalWithTax').val(response.totalWithTax);
                if (response.deliveryFee) $('#hiddenDeliveryFee').val(response.deliveryFee);
                if (response.customerBalance) $('#hiddenCustomerBalance').val(response.customerBalance);
                updateCheckoutModal();
            } else {
                $.ajax({
                    url: 'ajax/get_cart_html.php',
                    type: 'GET',
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#cart-container').html(res.html);
                            // Fetch updated financial data
                            fetchInitialData();
                        } else {
                            console.error('Error fetching cart HTML:', res.message);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX Error fetching cart HTML: ", textStatus, errorThrown);
                    }
                });
            }
        }

        // Function to update the checkout modal's displayed values
        function updateCheckoutModal() {
            var cartTotal = parseFloat($('#hiddenCartTotal').val()) || 0;
            var taxRate = parseFloat($('#hiddenTaxRate').val()) || 0;
            var taxAmount = parseFloat($('#hiddenTaxAmount').val()) || 0;
            var totalWithTax = parseFloat($('#hiddenTotalWithTax').val()) || 0;
            var deliveryFee = parseFloat($('#hiddenDeliveryFee').val()) || 0;
            var customerBalance = parseFloat($('#hiddenCustomerBalance').val()) || 0;

            var totalAmount = totalWithTax + deliveryFee;

            // Update the text in the modal
            $('#checkoutModal p:contains("Subtotal:") strong').text('₦' + cartTotal.toFixed(2));
            $('#checkoutModal p:contains("Delivery Fee:") strong').text('₦' + deliveryFee.toFixed(2));
            $('#checkoutModal p:contains("Tax (") strong').html('₦' + taxAmount.toFixed(2));
            $('#checkoutModal p:contains("Total Amount:") strong').text('₦' + totalAmount.toFixed(2));
            $('#checkoutModal p:contains("Your current balance is:") strong').text('₦' + customerBalance.toFixed(2));

            // Update the tax rate display in the modal
            $('#checkoutModal p:contains("Tax (")').html('Tax (' + taxRate.toFixed(2) + '%): <strong>₦' + taxAmount.toFixed(2) + '</strong>');

            // Enable/disable place order button based on balance
            var placeOrderButton = $('#checkoutModal button[form="checkoutForm"]');
            if (customerBalance < totalAmount && totalAmount > 0) {
                placeOrderButton.prop('disabled', true);
                if ($('#checkoutModal .alert-danger').length === 0) {
                    $('#checkoutForm').append('<div class="alert alert-danger mt-3">Your balance is insufficient for this purchase. Please top up your account.</div>');
                }
            } else {
                placeOrderButton.prop('disabled', false);
                $('#checkoutModal .alert-danger').remove();
            }
        }

        // Add to cart event handler
        $('#product-cards-container').on('submit', '.add-to-cart-form', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: 'ajax/add_to_cart.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        updateCartDisplay(response);
                    } else {
                        alert(response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error adding to cart: ", textStatus, errorThrown);
                    alert('Error adding item to cart. Please try again.');
                }
            });
        });

        // Remove from cart and clear cart event handlers
        $(document).on('submit', '.remove-from-cart-form, .clear-cart-form', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: 'ajax/update_cart.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        updateCartDisplay(response);
                    } else {
                        alert(response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error updating cart: ", textStatus, errorThrown);
                    alert('Error updating cart. Please try again.');
                }
            });
        });

        // Pagination
        function renderPagination(total, page, branchId) {
            var limit = 12;
            var totalPages = Math.ceil(total / limit);
            var paginationHtml = '';

            if (totalPages > 1) {
                paginationHtml += '<nav><ul class="pagination justify-content-center">';
                let currentPage = parseInt(page);

                paginationHtml += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '">';
                paginationHtml += '<a class="page-link" href="#" data-page="' + (currentPage - 1) + '" data-branch="' + branchId + '">Previous</a>';
                paginationHtml += '</li>';

                for (let i = 1; i <= totalPages; i++) {
                    paginationHtml += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '">';
                    paginationHtml += '<a class="page-link" href="#" data-page="' + i + '" data-branch="' + branchId + '">' + i + '</a>';
                    paginationHtml += '</li>';
                }

                paginationHtml += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '">';
                paginationHtml += '<a class="page-link" href="#" data-page="' + (currentPage + 1) + '" data-branch="' + branchId + '">Next</a>';
                paginationHtml += '</li>';

                paginationHtml += '</ul></nav>';
            }

            $('#pagination-container').html(paginationHtml);
        }

        $('#pagination-container').on('click', 'a.page-link', function(e) {
            e.preventDefault();
            var page = $(this).data('page');
            var branchId = $(this).data('branch');
            if (page) {
                fetchProducts(branchId, page);
            }
        });

        $('#branch').change(function() {
            var branchId = $(this).val();
            if (branchId) {
                fetchProducts(branchId, 1);
            } else {
                $('#product-cards-container').html('');
                $('#pagination-container').html('');
            }
        });
    </script>
</body>

</html>