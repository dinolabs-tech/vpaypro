<?php

include 'database/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

include('models/stock_transfers.php');
include('models/branches.php'); // Include the Branch model


$stockTransferModel = new StockTransfer($conn);
$branchModel = new Branch($conn); // Instantiate Branch model

// Handle delete action (if needed, though transfers are usually historical)
// For now, we'll just list them. If deletion is required, it would involve reversing stock changes.

$stockTransfers = $stockTransferModel->getAllBranchToBranchTransfers(); // Use the new method

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
              <h3 class="fw-bold mb-3">Manage Stock Transfers</h3>
              <nav>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                  <li class="breadcrumb-item active">Stock Transfers</li>
                </ol>
              </nav>
            </div>

           <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">All Stock Transfers</h5>

              <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
                <div class="alert alert-success">Stock transfer recorded successfully.</div>
              <?php elseif (isset($message)): ?>
                <div class="alert alert-danger">
                  <?= htmlspecialchars($message) ?>
                </div>
              <?php endif; ?>

              <a href="admin_add_stock_transfer.php" class="btn btn-primary mb-3 rounded"><i class="fas fa-plus"></i></a>
              <div class="table-responsive">
                <!-- Table with stripped rows -->
                <table class="table datatable" id="basic-datatables">
                  <thead>
                    <tr>
                      <th scope="col">S/N</th>
                      <th scope="col">From Branch</th>
                      <th scope="col">To Branch</th>
                      <th scope="col">Product</th>
                      <th scope="col">Quantity</th>
                      <th scope="col">Transfer Date</th>
                      <th scope="col">Notes</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($stockTransfers as $transfer): ?>
                      <tr>
                        <th scope="row"><?= $transfer['id'] ?></th>
                        <td><?= htmlspecialchars($transfer['from_branch_name']) ?></td>
                        <td><?= htmlspecialchars($transfer['to_branch_name']) ?></td>
                        <td><?= htmlspecialchars($transfer['product_name']) ?></td>
                        <td><?= htmlspecialchars($transfer['quantity']) ?></td>
                        <td><?= htmlspecialchars($transfer['transfer_date']) ?></td>
                        <td><?= htmlspecialchars($transfer['notes']) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
                <!-- End Table with stripped rows -->
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
  </body>
</html>
