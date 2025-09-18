<?php


include 'database/db_connection.php';
session_start();


if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

include('models/received_orders.php');

$purchaseOrderModel = new PurchaseOrder($conn);
$suppliers = $purchaseOrderModel->getAllSuppliers();
$products = $purchaseOrderModel->getAllProducts();

$message = '';
$purchaseOrder = null;

if (isset($_GET['id'])) {
  $po_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
  $purchaseOrder = $purchaseOrderModel->getPurchaseOrderById($po_id);
  if (!$purchaseOrder) {
    $message = "Purchase Order not found.";
  }
} else {
  $message = "No Purchase Order ID provided.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $purchaseOrder) {
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

  if ($purchaseOrderModel->updatePurchaseOrder($po_id, $supplier_id, $order_date, $expected_delivery_date, $status, $total_amount, $items)) {
    header("Location: supplier_received_orders.php?msg=updated");
    exit();
  } else {
    $message = "Error updating purchase order.";
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
            <h3 class="fw-bold mb-3">Edit Received Order</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item"><a href="supplier_received_orders.php">Manage Received Orders</a></li>
                <li class="breadcrumb-item active">Edit</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Received Order Details</h5>

                    <?php if ($message): ?>
                      <div class="alert alert-danger">
                        <?= htmlspecialchars($message) ?>
                      </div>
                    <?php endif; ?>

                    <?php if ($purchaseOrder): ?>
                      <form method="POST" class="row g-3">
                        <div class="col-md-6">
                          <label for="supplier_id" class="form-label">Supplier</label>

                          <select id="supplier_id" name="supplier_id" class="form-select form-control" required>
                            <!-- <option value="">Select Supplier</option> -->
                            <?php foreach ($suppliers as $supplier): ?>
                              <option readonly value="<?= $supplier['id'] ?>" <?= ($purchaseOrder['supplier_id'] == $supplier['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($supplier['companyname']) ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label for="order_date" class="form-label">Order Date</label>
                          <input readonly type="text" class="form-control" id="order_date" name="order_date" value="<?= htmlspecialchars($purchaseOrder['order_date']) ?>" required>
                        </div>
                        <div class="col-md-6">
                          <label for="expected_delivery_date" class="form-label">Expected Delivery Date</label>
                          <input type="text" class="form-control" id="expected_delivery_date" name="expected_delivery_date" value="<?= htmlspecialchars($purchaseOrder['expected_delivery_date']) ?>">
                        </div>
                        <div class="col-md-6">
                          <label for="status" class="form-label">Status</label>
                          <select id="status" name="status" class="form-select" required>
                            <option value="Accepted" <?= ($purchaseOrder['status'] == 'Accepted') ? 'selected' : '' ?>>Accept</option>
                            <option value="In-Transit" <?= ($purchaseOrder['status'] == 'In-Transit') ? 'selected' : '' ?>>In-Transit</option>
                            <option value="Cancelled" <?= ($purchaseOrder['status'] == 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                          </select>
                        </div>


                        <div class="col-12">
                          <h5 class="card-title">Order Items</h5>
                          <div id="order-items-container">
                            <?php if (isset($purchaseOrder) && !empty($purchaseOrder['items'])): ?>
                              <div class="row mb-3">
                                <div class="col-md-5"><strong>Product</strong></div>
                                <div class="col-md-3"><strong>Quantity</strong></div>
                                <div class="col-md-4"><strong>Unit Price</strong></div>
                              </div>
                              <?php foreach ($purchaseOrder['items'] as $item): ?>
                                <?php
                                // Find the product name from the $products array
                                $product_name = 'Unknown Product';
                                foreach ($products as $product) {
                                  if ($product['productid'] == $item['product_id']) {
                                    $product_name = $product['productname'];
                                    break;
                                  }
                                }
                                ?>
                                <div class="row mb-2">
                                  <div class="col-md-5">
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($product_name) ?>" readonly>
                                    <input type="hidden" name="product_id[]" value="<?= $item['product_id'] ?>">
                                  </div>
                                  <div class="col-md-3">
                                    <input type="text" class="form-control" name="quantity[]" value="<?= htmlspecialchars($item['quantity']) ?>" readonly>
                                  </div>
                                  <div class="col-md-4">
                                    <input type="text" class="form-control" name="unit_price[]" value="<?= htmlspecialchars(number_format($item['unit_price'], 2)) ?>" readonly>
                                  </div>
                                </div>
                              <?php endforeach; ?>
                            <?php else: ?>
                              <p>No items found for this purchase order.</p>
                            <?php endif; ?>
                          </div>
                        </div>

                        <div class="col-12 text-center">
                          <button type="submit" class="btn btn-primary rounded"><i class="fas fa-save"></i></button>
                          <a href="supplier_received_orders.php" class="btn btn-secondary rounded"><i class="fas fa-window-close"></i></a>
                        </div>
                      </form>
                    <?php endif; ?>

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
</body>

</html>