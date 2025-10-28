<?php
include('models/admin_orders.php');
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
            <h3 class="fw-bold mb-3">Manage Orders</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Orders</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">All Orders</h5>

                    <!-- Table with stripped rows -->
                    <div class="table-responsive">
                      <table class="table datatable" id="basic-datatables">
                        <thead>
                          <tr>
                            <th scope="col">S/N</th>
                            <th scope="col">Customer</th>
                            <th scope="col">Total Amount</th>
                            <th scope="col">Status</th>
                            <th scope="col">Order Date</th>
                            <?php if ($_SESSION['role'] != 'Inventory Manager') { ?>
                              <th scope="col">Action</th>
                            <?php } ?>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($orders as $order): ?>
                            <tr>
                              <th scope="row"><?= $order['id'] ?></th>
                              <td><?= htmlspecialchars($order['name']) ?></td>
                              <td>₦ <?= number_format($order['total_amount'], 2) ?></td>
                              <td><?= htmlspecialchars($order['status']) ?></td>
                              <td><?= htmlspecialchars($order['order_date']) ?></td>
                              <?php if ($_SESSION['role'] != 'Inventory Manager') { ?>
                                <td>
                                  <a href="admin_order_details.php?id=<?= $order['id'] ?>" class="btn btn-info btn-sm">View</a>
                                </td>
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