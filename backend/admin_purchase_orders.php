<?php

include 'database/db_connection.php';
session_start();


if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

include('models/purchase_orders.php');

$purchaseOrderModel = new PurchaseOrder($conn);

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
  $po_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
  if ($purchaseOrderModel->deletePurchaseOrder($po_id)) {
    header("Location: admin_purchase_orders.php?msg=deleted");
    exit();
  } else {
    $message = "Error deleting purchase order.";
  }
}

$purchaseOrders = $purchaseOrderModel->getAllPurchaseOrders();

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
              <h3 class="fw-bold mb-3">Manage Purchase Orders</h3>
              <nav>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                  <li class="breadcrumb-item active">Purchase Orders</li>
                </ol>
              </nav>
            </div>

             <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">All Purchase Orders</h5>

              <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
                <div class="alert alert-success">Purchase order added successfully.</div>
              <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
                <div class="alert alert-success">Purchase order updated successfully.</div>
              <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-success">Purchase order deleted successfully.</div>
              <?php elseif (isset($message)): ?>
                <div class="alert alert-danger">
                  <?= htmlspecialchars($message) ?>
                </div>
              <?php endif; ?>

              <?php if ($_SESSION['role'] != 'Inventory Manager') { ?>
                <a href="admin_add_purchase_order.php" class="btn btn-primary rounded mb-3"><i class="fas fa-plus"></i></a>
              <?php } ?>
              <!-- Table with stripped rows -->
              <div class="table-responsive">
                <table class="table datatable" id="basic-datatables">
                  <thead>
                    <tr>
                      <th scope="col">S/N</th>
                      <th scope="col">Supplier</th>
                      <th scope="col">Order Date</th>
                      <th scope="col">Expected Delivery</th>
                      <th scope="col">Status</th>
                      <th scope="col">Total Amount</th>
                      <?php if ($_SESSION['role'] != 'Inventory Manager') { ?>
                        <th scope="col">Actions</th>
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($purchaseOrders as $po): ?>
                      <tr>
                        <th scope="row"><?= $po['id'] ?></th>
                        <td><?= htmlspecialchars($po['supplier_name']) ?></td>
                        <td><?= htmlspecialchars($po['order_date']) ?></td>
                        <td><?= htmlspecialchars($po['expected_delivery_date']) ?></td>
                        <td><?= htmlspecialchars($po['status']) ?></td>
                        <td><?= number_format($po['total_amount'], 2) ?></td>

                        <?php if ($_SESSION['role'] != 'Inventory Manager') { ?>
                          <?php if ($po['status'] == 'Accepted') { ?>

                            <?php } elseif ($po['status'] == 'Received') { ?>

                          <?php } elseif ($po['status'] == 'In-Transit') { ?>
                            <td>
                              <a href="admin_edit_purchase_order.php?id=<?= $po['id'] ?>" class="btn btn-info btn-sm rounded"><i class="fas fa-edit"></i></a>
                            </td>
                          <?php } else { ?>
                            <td>
                              <a href="admin_edit_purchase_order.php?id=<?= $po['id'] ?>" class="btn btn-info btn-sm rounded mb-2"><i class="fas fa-edit"></i></a>
                              <a href="admin_purchase_orders.php?action=delete&id=<?= $po['id'] ?>" class="btn btn-danger btn-sm rounded mb-2" onclick="return confirm('Are you sure you want to delete this purchase order?')"><i class="fas fa-trash"></i></a>
                            </td>
                          <?php } ?>

                        <?php } ?>

                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <!-- End Table with stripped rows -->

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
