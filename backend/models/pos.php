<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './phpmailer/src/Exception.php';
require './phpmailer/src/PHPMailer.php';
require './phpmailer/src/SMTP.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

include './database/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch branch_id for the logged-in user
$user_branch_id = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT branch_id FROM login WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    if ($user_data) {
        $user_branch_id = $user_data['branch_id'];
    }
    $stmt->close();
}

if ($user_branch_id === null) {
    die("User's branch ID not found. Please ensure the user is assigned to a branch.");
}

function generate_invoice($cart, $transactionID, $customer_name = null, $taxRate = 0, $taxAmount = 0, $totalWithTax = 0)
{
    $total_amount = 0;

    $html = "<h1 style='text-align: center;'>Dinolabs Tech Services</h1>\n";
    $html .= "<p style='text-align: center;'>5th Floor, Wing-B, TISCO building Alagbaka,Akure, Ondo state.</p>\n";
    $html .= "<p style='text-align: center;'>enquiries@dinolabstech.com</p>\n";
    $html .= "<p style='text-align: center;'>+234 704 324 7461</p>\n";
    $html .= "<h1 style='text-align: center;'>Invoice</h1>\n";
    $html .= "<p style='text-align: center;'>Invoice Number: " . htmlspecialchars($transactionID) . "</p>\n";
    $html .= "<p style='text-align: center;'>Transaction ID: " . htmlspecialchars($transactionID) . "</p>\n";
    if ($customer_name) {
        $html .= "<p style='text-align: center;'>Customer: " . htmlspecialchars($customer_name) . "</p>\n";
    }

    $html .= "<table width='100%' border='0' cellpadding='5' cellspacing='5'>\n";
    $html .= "<tr><th>ID</th><th>Product</th><th>Price</th><th>Discount</th><th>Quantity</th><th>Total</th></tr>\n";

    foreach ($cart as $item) {
        $discounted_price = $item['price'] * (1 - ($item['discount'] / 100));
        $line_total = $discounted_price * $item['qty'];
        $total_amount += $line_total;

        $html .= "<tr>"
            . "<td style='text-align: center;'>" . htmlspecialchars($item['product_id']) . "</td>"
            . "<td style='text-align: center;'>" . htmlspecialchars($item['product_name']) . "</td>"
            . "<td style='text-align: center;'>" . number_format($item['price'], 2) . "</td>"
            . "<td style='text-align: center;'>" . number_format($item['discount'], 0) . "%</td>"
            . "<td style='text-align: center;'>" . htmlspecialchars($item['qty']) . "</td>"
            . "<td style='text-align: center;'>" . number_format($line_total, 2) . "</td>"
            . "</tr>\n";
    }

    $html .= "<tr><td colspan='5' style='text-align: right;'><strong>Subtotal</strong></td>"
        . "<td style='text-align: center;'><strong>" . number_format($total_amount, 2) . "</strong></td></tr>\n";
    $html .= "<tr><td colspan='5' style='text-align: right;'><strong>V.A.T. (" . number_format($taxRate, 2) . "%)</strong></td>"
        . "<td style='text-align: center;'><strong>" . number_format($taxAmount, 2) . "</strong></td></tr>\n";
    $html .= "<tr><td colspan='5' style='text-align: right;'><strong>Total</strong></td>"
        . "<td style='text-align: center;'><strong>" . number_format($totalWithTax, 2) . "</strong></td></tr>\n";
    $html .= "</table>\n";

    return $html;
}


// Initialization
$message = '';
$products = [];

// Load product list for the current branch or all products for Superuser/CEO
$productQuery = "
    SELECT p.productid, p.productname, p.sellprice, bpi.quantity AS qty, p.unitprice, p.discount, p.sku
    FROM product p
    JOIN branch_product_inventory bpi ON p.productid = bpi.productid
";

$queryParams = [];
$queryTypes = "";
$user_branch_id = $_SESSION['branch_id'];

if (isset($_SESSION['role']) && $_SESSION['role'] !== 'Superuser' && $_SESSION['role'] !== 'CEO') {
    $productQuery .= " WHERE bpi.branch_id = ?";
    $queryTypes .= "i";
    $queryParams[] = $user_branch_id;
}

// Prepare and execute the statement
if (!empty($queryParams)) {
    $stmt = $conn->prepare($productQuery);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $bind_params = [];
    $bind_params[] = $queryTypes;
    foreach ($queryParams as $key => $value) {
        $bind_params[] = &$queryParams[$key]; // Pass by reference
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_params);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $result = $conn->query($productQuery);
}
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    die("Error fetching products: " . $conn->error);
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'add_to_cart':
            $product_id = (int) $_POST['product_id'];
            $product_name = $_POST['product_name'];
            $price = (float) $_POST['price'];
            $qty_to_add = (int) $_POST['qty'];

            // Fetch available stock from branch_product_inventory
            $stmt = $conn->prepare("SELECT quantity FROM branch_product_inventory WHERE productid = ? AND branch_id = ?");
            $stmt->bind_param("ii", $product_id, $user_branch_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $inventory = $res->fetch_assoc();
            $stmt->close();

            $available_stock = $inventory ? (int)$inventory['quantity'] : 0;

            // Calculate current quantity in cart
            $current_cart_qty = 0;
            if (isset($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    if ($item['product_id'] == $product_id) {
                        $current_cart_qty = $item['qty'];
                        break;
                    }
                }
            }

            // Check if adding the new quantity exceeds available stock
            if (($current_cart_qty + $qty_to_add) > $available_stock) {
                $message = "Cannot add to cart. Exceeds available stock of " . $available_stock . ".";
                break;
            }

            // Fetch cost, discount, and SKU
            $stmt = $conn->prepare("SELECT unitprice, discount, sku FROM product WHERE productid = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $product = $res->fetch_assoc();
            $stmt->close();

            if ($product) {
                $cost_price = (float) $product['unitprice'];
                $discount = (float) $product['discount'];
                $sku = $product['sku']; // Fetch SKU
                $unit_profit = $price - $cost_price;

                if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

                $product_exists = false;
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['product_id'] == $product_id) {
                        $item['qty'] += $qty_to_add;
                        $product_exists = true;
                        break;
                    }
                }

                if (!$product_exists) {
                    $_SESSION['cart'][] = [
                        'product_id' => $product_id,
                        'product_name' => $product_name,
                        'price' => $price,
                        'qty' => $qty_to_add,
                        'unit_profit' => $unit_profit,
                        'discount' => $discount
                    ];
                }
            } else {
                $message = "Product not found.";
            }
            break;

        case 'remove_from_cart':
            $product_id = $_POST['product_id'];
            $_SESSION['cart'] = array_filter($_SESSION['cart'], function ($item) use ($product_id) {
                return $item['product_id'] != $product_id;
            });
            break;

        case 'clear_cart':
            unset($_SESSION['cart']);
            break;

        case 'checkout':
            if (empty($_SESSION['cart'])) {
                $message = "Cart is empty. Add products before checkout.";
                break;
            }

            $customer_id = $_POST['customer_id'] ?? null;
            $customer_country = $_POST['customer_country'] ?? null;
            $customer_state = $_POST['customer_state'] ?? null;

            // Include and instantiate TaxRate model
            include_once 'models/tax_rates.php';
            $taxRateModel = new TaxRate($conn);

            $cartTotal = 0;
            foreach ($_SESSION['cart'] as $item) {
                $discounted_price = $item['price'] * (1 - ($item['discount'] / 100));
                $itemTotal = $discounted_price * $item['qty'];
                $cartTotal += $itemTotal;
            }

            // Calculate tax
            $taxRate = 0;
            if ($customer_country) {
                $taxRate = $taxRateModel->getTaxRateByLocation($customer_country, $customer_state);
            }
            $taxAmount = ($cartTotal * $taxRate) / 100;
            $totalWithTax = $cartTotal + $taxAmount;

            if ($customer_id) {
                // Purchase by customer
                $stmt = $conn->prepare("SELECT balance, account_status FROM customers WHERE customer_id = ?");
                $stmt->bind_param("s", $customer_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $customer = $result->fetch_assoc();
                $stmt->close();

                if (!$customer) {
                    $message = "Invalid customer ID.";
                    break;
                }

                if ($customer['account_status'] === 'disabled') {
                    $message = "This account is disabled and cannot make purchases.";
                    break;
                }

                if ($customer['balance'] < $totalWithTax) {
                    $message = "Insufficient balance.";
                    break;
                }

                // Deduct from balance
                $new_balance = $customer['balance'] - $totalWithTax;
                $stmt = $conn->prepare("UPDATE customers SET balance = ? WHERE customer_id = ?");
                $stmt->bind_param("ds", $new_balance, $customer_id);
                $stmt->execute();
                $stmt->close();

                // Record customer transaction
                // Assuming $_SESSION['user_id'] holds the ID of the logged-in staff member
                $processed_by_user_id = $_SESSION['user_id'] ?? null;
                $stmt = $conn->prepare("INSERT INTO customer_transactions (customer_id, transaction_type, amount, processed_by_user_id) VALUES (?, 'purchase', ?, ?)");
                $stmt->bind_param("sds", $customer_id, $totalWithTax, $processed_by_user_id);
                $stmt->execute();
                $stmt->close();

                // -- EMAIL NOTIFICATION --
                // After a successful transaction, fetch the customer's details to send a confirmation email.
                $stmt = $conn->prepare("SELECT email, firstname, lastname FROM customers WHERE customer_id = ?");
                $stmt->bind_param("s", $customer_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $customer_data = $result->fetch_assoc();
                $stmt->close();

                // Check if the customer has an email address before proceeding.
                if ($customer_data && !empty($customer_data['email'])) {
                    $customer_email = $customer_data['email'];
                    $customer_name_for_email = trim($customer_data['firstname'] . ' ' . $customer_data['lastname']);

                    // Generate the HTML invoice to be included in the email body.
                    $invoice_for_email = generate_invoice($_SESSION['cart'], $transactionID, $customer_name_for_email, $taxRate, $taxAmount, $totalWithTax);

                    try {
                        // Initialize PHPMailer.
                        $mail = new PHPMailer(true);

                        // Configure SMTP settings for the email server.
                        $mail->isSMTP();
                        $mail->Host       = 'mail.dinolabstech.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'enquiries@dinolabstech.com';
                        $mail->Password   = 'Dinolabs@11';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        // Set the sender and recipient.
                        $mail->setFrom('enquiries@dinolabstech.com', 'Dinolabs Tech Services');
                        $mail->addAddress($customer_email, $customer_name_for_email);

                        // Set email content.
                        $mail->isHTML(true);
                        $mail->Subject = 'Your Purchase Confirmation - Order ' . $transactionID;
                        $mail->Body    = "
                            <p>Dear $customer_name_for_email,</p>
                            <p>Thank you for your purchase from Dinolabs Tech Services. We've received your order and are getting it ready for you.</p>
                            <p>Here is a summary of your order:</p>
                            $invoice_for_email
                            <p>We appreciate your business!</p>
                            <p>Best regards,<br>The Dinolabs Team</p>
                        ";

                        // Send the email.
                        $mail->send();
                    } catch (Exception $e) {
                        // If the email fails to send, log the error instead of stopping the checkout process.
                        error_log("PHPMailer Error: " . $mail->ErrorInfo);
                    }
                }
            }

            // Standard checkout process
            $credit = (float) ($_POST['credit_amount'] ?? 0);
            $refund = 0;
            $cashier = $_SESSION['user_id'] ?? 'Unknown';
            $status = 'sales';
            $description = "Sales";
            $studentname = '';
            $updates = [];

            // Generate transaction ID once
            $result = $conn->query("SELECT COUNT(*) AS total FROM transactiondetails");
            $row = $result->fetch_assoc();
            $transaction_count = $row['total'] + 1;
            $transactionID = "INV-" . str_pad($transaction_count, 5, "0", STR_PAD_LEFT);

            foreach ($_SESSION['cart'] as $item) {
                $amount = ($item['price'] * (1 - $item['discount'] / 100)) * $item['qty'];
                $profit = $item['unit_profit'] * $item['qty'];

                // Update product quantity in branch_product_inventory
                $stmt = $conn->prepare("UPDATE branch_product_inventory SET quantity = quantity - ? WHERE productid = ? AND branch_id = ?");
                $stmt->bind_param("iii", $item['qty'], $item['product_id'], $user_branch_id);
                $stmt->execute();
                $stmt->close();

                // Insert into transactiondetails
                $insertQuery = "INSERT INTO transactiondetails 
                    (transactionID, productid, productname, description, units, amount, transactiondate, profit, cashier, status, discount, refund, credit)
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bind_param(
                    "sissdddssddd",
                    $transactionID,
                    $item['product_id'],
                    $item['product_name'],
                    $description,
                    $item['qty'],
                    $amount,
                    $profit,
                    $cashier,
                    $status,
                    $item['discount'],
                    $refund,
                    $credit
                );
                $stmt->execute();
                $stmt->close();

                $updates[] = "{$item['qty']} x {$item['product_name']}";
            }

            // Generate invoice
            $customer_name = $_POST['customer_name'] ?? null;
            $invoice = generate_invoice($_SESSION['cart'], $transactionID, $customer_name, $taxRate, $taxAmount, $totalWithTax);
            $_SESSION['invoice'] = $invoice;

            unset($_SESSION['cart']);
            $message = "Checkout successful for items: " . implode(", ", $updates);
            break;
    }

    if ($action !== 'checkout') {
        // Avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
