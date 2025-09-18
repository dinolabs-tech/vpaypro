<?php
include('models/customer_orders.php');
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
            <h3 class="fw-bold mb-3">My Orders</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="customer_dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">My Orders</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Order History</h5>
                    <div class="table-responsive">

                      <!-- Table with stripped rows -->
                      <table class="table datatable" id="basic-datatables">
                        <thead>
                          <tr>
                            <th scope="col">S/N</th>
                            <th scope="col">Total Amount</th>
                            <th scope="col">Status</th>
                            <th scope="col">Order Date</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($orders as $order): ?>
                            <tr>
                              <th scope="row"><?= $order['id'] ?></th>
                              <td>₦<?= number_format($order['total_amount'], 2) ?></td>
                              <td><?= htmlspecialchars($order['status']) ?></td>
                              <td><?= htmlspecialchars($order['order_date']) ?></td>
                              <td>
                                <a href="customer_order_details.php?id=<?= $order['id'] ?>" class="btn btn-info btn-sm">View</a>
                              </td>
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