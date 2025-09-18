<?php
include('models/pos.php');
include_once('models/tax_rates.php'); // Include the TaxRate model


// Initialize TaxRate model
$taxRateModel = new TaxRate($conn);

// Generate Invoice ID
$sql = "SELECT COUNT(*) AS total FROM transactiondetails";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$invoice_count = $row['total'] + 1;
$invoice_id = "INV-" . str_pad($invoice_count, 5, "0", STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="en">
  <?php include('components/head.php'); ?>
  <body>
    <div class="wrapper">
      <?php include('components/sidebar.php'); ?>

      <div class="main-panel">
        <?php include('components/navbar.php'); ?>

        <div class="container">
          <div class="page-inner">
            <div>
              <h3 class="fw-bold mb-3">POS (<?= $_SESSION['country']; ?>)</h3>
              <nav>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                  <li class="breadcrumb-item active">Point Of Sales</li>
                </ol>
              </nav>
            </div>

            <section class="section">
      <div class="row">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-6">
              <div class="card card-round">
                <div class="card-header">
                  <h4 class="card-title">Product List</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered datatable" id="basic-datatables">
                      <thead>
                        <tr>
                          <th>Product Name</th>
                          <th>SKU</th>
                          <th>Price</th>
                          <th>Qty</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($products as $product): ?>
                          <tr>
                            <td><?= htmlspecialchars($product['productname'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($product['sku'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= number_format($product['sellprice'], 2) ?></td>
                            <td><?= number_format($product['qty']) ?></td>
                            <td>
                              <?php if ($product['qty'] > 0): ?>
                                <form method="POST" class="d-inline">
                                  <input type="hidden" name="action" value="add_to_cart">
                                  <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['productid']) ?>">
                                  <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['productname']) ?>">
                                  <input type="hidden" name="price" value="<?= htmlspecialchars($product['sellprice']) ?>">
                                  <input type="hidden" name="sku" value="<?= htmlspecialchars($product['sku'] ?? '') ?>"> <!-- Add hidden SKU input -->
                                  <input type="number" name="qty" value="1" min="1" max="<?= (int) $product['qty'] ?>" class="form-control mb-2" style="width:60px;">

                                  
                                  <button type="submit" class="btn btn-success rounded btn-sm">
                                    <i class="fas fa-cart-plus"></i>
                                  </button>

                                </form>
                              <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>Out of Stock</button>
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <!-- Cart Section -->
              <div class="card mt-4 d-print-none">
                <div class="card-body">
                  <h3 class="card-title mb-3">Cart</h3>

                  <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                    <div class="table-responsive">
                      <table class="table table-bordered cart-table" id="basic-datatables">
                        <thead>
                          <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Discount</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <?php $cartTotal = 0; ?>
                        <tbody>
                          <?php foreach ($_SESSION['cart'] as $item):
                            $discounted_price = $item['price'] * (1 - ($item['discount'] / 100));
                            $itemTotal = $discounted_price * $item['qty'];
                            $cartTotal += $itemTotal;
                          ?>
                            
                            <tr>
                              <td><?= htmlspecialchars($item['product_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                              <td><?= number_format($item['price'], 2) ?></td>
                              <td><?= htmlspecialchars($item['discount']) ?>%</td>
                              <td><?= htmlspecialchars($item['qty']) ?></td>
                              <td><?= number_format($itemTotal, 2) ?></td>
                              <td>
                                <form method="POST" class="d-inline">
                                  <input type="hidden" name="action" value="remove_from_cart">
                                  <input type="hidden" name="product_id" value="<?= htmlspecialchars($item['product_id']) ?>">
                                  <button type="submit" class="btn btn-danger rounded btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                              </td>
                            </tr>
                            <?php $_SESSION['pname'] = $item['product_name']?>
                          <?php endforeach; ?>

                          <?php
                          // Tax calculation
                          $taxRate = 0;
                          $countryForTax = null;
                          $stateForTax = null;

                          // Prioritize logged-in staff's country if available
                          if (isset($_SESSION['role']) && ($_SESSION['role'] == 'Administrator' || $_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'Accounts' || $_SESSION['role'] == 'Staff' || $_SESSION['role'] == 'Sales') && isset($_SESSION['country'])) {
                            $countryForTax = $_SESSION['country'];
                            $stateForTax = $_SESSION['state'] ?? null;
                          } elseif (isset($_POST['customer_country'])) {
                            // Fallback to customer's country if staff country not used
                            $countryForTax = $_POST['customer_country'];
                            $stateForTax = $_POST['customer_state'] ?? null;
                          }

                          if ($countryForTax) {
                            $taxRate = $taxRateModel->getTaxRateByLocation($countryForTax, $stateForTax);
                          }

                          $taxAmount = ($cartTotal * $taxRate) / 100;
                          $totalWithTax = $cartTotal + $taxAmount;
                          ?>
                          <tr>
                            <td colspan="4" class="text-end fw-bold">Subtotal</td>
                            <td colspan="2" class="fw-bold">₦<?= number_format($cartTotal, 2) ?></td>
                          </tr>
                          <tr>
                            <td colspan="4" class="text-end fw-bold">V.A.T. (<?= number_format($taxRate, 2) ?>%)</td>
                            <td colspan="2" class="fw-bold">₦<?= number_format($taxAmount, 2) ?></td>
                          </tr>
                          <tr>
                            <td colspan="4" class="text-end fw-bold">Total</td>
                            <td colspan="2" class="fw-bold">₦<?= number_format($totalWithTax, 2) ?></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                    <!-- Buttons -->
                    <div class="mt-3">
                      <form method="POST" class="d-inline">
                        <input type="hidden" name="action" value="clear_cart">
                        <button type="submit" class="btn btn-warning rounded">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>

                      <form method="POST" class="d-inline ms-2" id="checkoutForm">
                        <input type="hidden" name="action" value="checkout">
                        <input type="hidden" name="invoice_id" value="<?= htmlspecialchars($invoice_id) ?>">
                        <input type="hidden" name="customer_id" id="checkoutCustomerId">
                        <input type="hidden" name="customer_name" id="checkoutCustomerName">
                        <button type="submit" class="btn btn-success rounded" id="checkoutButton">
                          <i class="fas fa-check-double"></i> 
                        </button>
                      </form>
                    </div>
                  <?php else: ?>
                    <p class="text-center mt-3">Your cart is empty.</p>
                  <?php endif; ?>

                  <!-- Purchase by Customer ID -->
                  <div class="mt-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="purchaseByCustomerIdCheck">
                      <label class="form-check-label" for="purchaseByCustomerIdCheck">
                        Purchase by Customer ID
                      </label>
                    </div>
                  </div>

                  <div id="customerIdForm" style="display: none;">
                    <div class="input-group mt-3">
                      <input type="text" id="customerId" class="form-control" placeholder="Enter Customer ID">
                      <button id="verifyCustomerId" class="btn btn-primary">Verify</button>
                    </div>
                    <div id="customerDetails" class="mt-3"></div>
                  </div>
                  <!-- End Purchase by Customer ID -->



                  <?php if ($message): ?>
                    <div class="alert alert-info mt-3">
                      <?= htmlspecialchars($message) ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
          </div>
        </div>

        <?php include('components/footer.php'); ?>
      </div>
    </div>
    <?php include('components/script.php'); ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      const purchaseByCustomerIdCheck = document.getElementById('purchaseByCustomerIdCheck');
      const customerIdForm = document.getElementById('customerIdForm');
      const verifyCustomerId = document.getElementById('verifyCustomerId');
      const customerIdInput = document.getElementById('customerId');
      const customerDetails = document.getElementById('customerDetails');
      const checkoutButton = document.getElementById('checkoutButton');

      purchaseByCustomerIdCheck.addEventListener('change', function() {
        if (this.checked) {
          customerIdForm.style.display = 'block';
        } else {
          customerIdForm.style.display = 'none';
          customerDetails.innerHTML = '';
        }
      });

      verifyCustomerId.addEventListener('click', function() {
        const customerId = customerIdInput.value;
        if (customerId) {
          fetch('ajax/get_customer_details.php?customer_id=' + customerId)
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                document.getElementById('checkoutCustomerId').value = customerId;
                document.getElementById('checkoutCustomerName').value = data.name;
                // Store country and state in hidden fields
                const customerCountryInput = document.createElement('input');
                customerCountryInput.type = 'hidden';
                customerCountryInput.name = 'customer_country';
                customerCountryInput.value = data.country;
                document.getElementById('checkoutForm').appendChild(customerCountryInput);

                const customerStateInput = document.createElement('input');
                customerStateInput.type = 'hidden';
                customerStateInput.name = 'customer_state';
                customerStateInput.value = data.state;
                document.getElementById('checkoutForm').appendChild(customerStateInput);

                customerDetails.innerHTML = `
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">${data.name}</h5>
                      <p>Balance: ₦${data.balance}</p>
                      <p>Location: ${data.state}, ${data.country}</p>
                      <img src="assets/img/${data.profile_picture}" alt="Profile" class="img-fluid rounded-circle" style="width: 120px;height:120px;">
                    </div>
                  </div>
                `;
              } else {
                document.getElementById('checkoutCustomerId').value = '';
                document.getElementById('checkoutCustomerName').value = '';
                customerDetails.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
              }
            })
            .catch(error => {
              console.error('Error:', error);
              customerDetails.innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
            });
        }
      });
    });
  </script>

  <?php
  if (isset($_SESSION['invoice'])) {
    $invoice_html = $_SESSION['invoice'];
    unset($_SESSION['invoice']);
    echo <<<HTML
      <script>
        var printWindow = window.open('', '', 'width=800,height=600');
        printWindow.document.write(`$invoice_html`);
        printWindow.document.close();
        printWindow.print();
      </script>
HTML;
  }
  ?>
  </body>
</html>
