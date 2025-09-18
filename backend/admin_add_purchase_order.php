<?php

include 'database/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

include('models/purchase_orders.php');


$purchaseOrderModel = new PurchaseOrder($conn);
$suppliers = $purchaseOrderModel->getAllSuppliers();
$products = $purchaseOrderModel->getAllProducts();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $supplier_id = filter_var($_POST['supplier_id'], FILTER_SANITIZE_NUMBER_INT);
  $order_date = $_POST['order_date'];
  $expected_delivery_date = $_POST['expected_delivery_date'];
  $status = $_POST['status'];

  $product_ids = $_POST['product_id'] ?? [];
  $quantities = $_POST['quantity'] ?? [];
  $unit_prices = $_POST['unit_price'] ?? [];

  $items = [];
  $total_amount = 0;

  for ($i = 0; $i < count($product_ids); $i++) {
    $product_id = filter_var($product_ids[$i], FILTER_SANITIZE_NUMBER_INT);
    $quantity = filter_var($quantities[$i], FILTER_SANITIZE_NUMBER_INT);
    $unit_price = filter_var($unit_prices[$i], FILTER_VALIDATE_FLOAT);
    $subtotal = $quantity * $unit_price;
    $total_amount += $subtotal;

    $items[] = [
      'product_id' => $product_id,
      'quantity' => $quantity,
      'unit_price' => $unit_price,
      'subtotal' => $subtotal
    ];
  }

  if ($purchaseOrderModel->createPurchaseOrder($supplier_id, $order_date, $expected_delivery_date, $status, $total_amount, $items)) {
    header("Location: admin_purchase_orders.php?msg=added");
    exit();
  } else {
    $message = "Error adding purchase order.";
  }
}

$conn->close();
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
            <h3 class="fw-bold mb-3">Add New Purchase Order</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item"><a href="admin_purchase_orders.php">Manage Purchase Orders</a></li>
                <li class="breadcrumb-item active">Add New</li>
              </ol>
            </nav>
          </div>
<section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Purchase Order Details</h5>

              <?php if ($message): ?>
                <div class="alert alert-danger">
                  <?= htmlspecialchars($message) ?>
                </div>
              <?php endif; ?>

              <form method="POST" class="row g-3">
                <div class="col-md-6">
                  <label for="supplier_id" class="form-label">Supplier</label>
                  <select id="supplier_id" name="supplier_id" class="form-select" required>
                    <option value="">Select Supplier</option>
                    <?php foreach ($suppliers as $supplier): ?>
                      <option value="<?= $supplier['id'] ?>"><?= htmlspecialchars($supplier['companyname']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="order_date" class="form-label">Order Date</label>
                  <input type="date" class="form-control" id="order_date" name="order_date" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-6">
                  <label for="expected_delivery_date" class="form-label">Expected Delivery Date</label>
                  <input type="date" class="form-control" id="expected_delivery_date" name="expected_delivery_date">
                </div>
                <div class="col-md-6">
                  <label for="status" class="form-label">Status</label>
                  <select id="status" name="status" class="form-select" required>
                    <option value="Pending">Pending</option>
                    <option value="Ordered">Ordered</option>
                    <option value="Received">Received</option>
                    <option value="Cancelled">Cancelled</option>
                  </select>
                </div>

                <div class="col-12">
                  <h5 class="card-title">Order Items</h5>
                  <div id="order-items-container">
                    <!-- Dynamic item rows will be added here -->
                  </div>
                  <button type="button" class="btn btn-success btn-sm rounded" id="add-item">Add Item</button>
                </div>

                <div class="col-12 text-center">
                  <button type="submit" class="btn btn-primary rounded">Create Purchase Order</button>
                  <a href="admin_purchase_orders.php" class="btn btn-secondary rounded">Cancel</a>
                </div>
              </form>

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
      const addItemBtn = document.getElementById('add-item');
      const orderItemsContainer = document.getElementById('order-items-container');
      const products = <?= json_encode($products) ?>;

      addItemBtn.addEventListener('click', function() {
        addItemRow();
      });

      orderItemsContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-item')) {
          event.target.closest('.order-item').remove();
        }
      });

      function addItemRow(item = {}) {
        const newRow = document.createElement('div');
        newRow.classList.add('row', 'mb-3', 'order-item');
        newRow.innerHTML = `
          <div class="col-md-4 mt-3 mb-3">
            <select name="product_id[]" class="form-select product-select" required>
              <option value="">Select Product</option>
              ${products.map(product => `<option value="${product.productid}" data-unitprice="${product.unitprice}" ${item.product_id == product.productid ? 'selected' : ''}>${product.productname}</option>`).join('')}
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <input type="number" name="quantity[]" class="form-control item-quantity" placeholder="Quantity" value="${item.quantity || ''}" required min="1">
          </div>
          <div class="col-md-3 mb-3">
            <input type="number" step="0.01" name="unit_price[]" class="form-control item-unit-price" placeholder="Unit Price" value="${item.unit_price || ''}" required>
          </div>
          <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-sm remove-item">X</button>
          </div>
        `;
        orderItemsContainer.appendChild(newRow);

        // Set initial unit price if product is pre-selected
        if (item.product_id) {
          const productSelect = newRow.querySelector('.product-select');
          const selectedOption = productSelect.options[productSelect.selectedIndex];
          const unitPriceInput = newRow.querySelector('.item-unit-price');
          if (selectedOption && selectedOption.dataset.unitprice) {
            unitPriceInput.value = selectedOption.dataset.unitprice;
          }
        }

        // Add event listener for product selection change to update unit price
        newRow.querySelector('.product-select').addEventListener('change', function() {
          const selectedOption = this.options[this.selectedIndex];
          const unitPriceInput = newRow.querySelector('.item-unit-price');
          if (selectedOption && selectedOption.dataset.unitprice) {
            unitPriceInput.value = selectedOption.dataset.unitprice;
          } else {
            unitPriceInput.value = '';
          }
        });
      }

      // If in edit mode, populate existing items
      <?php if (isset($purchaseOrder) && !empty($purchaseOrder['items'])): ?>
        <?php foreach ($purchaseOrder['items'] as $item): ?>
          addItemRow(<?= json_encode($item) ?>);
        <?php endforeach; ?>
      <?php endif; ?>

      // Add one empty row if no items exist (for new purchase order)
      <?php if (!isset($purchaseOrder) || empty($purchaseOrder['items'])): ?>
        addItemRow();
      <?php endif; ?>
    });
  </script>
</body>

</html>