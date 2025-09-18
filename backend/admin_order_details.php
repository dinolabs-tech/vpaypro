<?php
include('models/admin_order_details.php');
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
                <li class="breadcrumb-item active">Order Details</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Order #<?= htmlspecialchars($order['id']) ?></h5>

                    <div class="row">
                      <div class="col-md-6">
                        <h6><strong>Customer Details</strong></h6>
                        <p><strong>ID:</strong> <?= htmlspecialchars($order['customer_id']) ?></p>
                        <p><strong>Name:</strong> <?= htmlspecialchars($customer['name']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($customer['phone']) ?></p>
                        <p><strong>Country:</strong> <?= htmlspecialchars($customer['country']) ?></p>
                        <p><strong>State:</strong> <?= htmlspecialchars($customer['state']) ?></p>
                        <p><strong>Delivery Address:</strong> <?= htmlspecialchars($order['delivery_address']) ?></p>
                      </div>
                      <div class="col-md-6">
                        <h6><strong>Order Summary</strong></h6>
                        <p><strong>Total Amount:</strong> ₦<?= number_format($order['total_amount'], 2) ?></p>
                        <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
                        <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
                      </div>
                    </div>

                    <h6 class="mt-4">Ordered Items</h6>
                    <div class="table-responsive">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($order_items as $item): ?>
                            <tr>
                              <td><?= htmlspecialchars($item['productname']) ?></td>
                              <td><?= htmlspecialchars($item['quantity']) ?></td>
                              <td>₦<?= number_format($item['price'], 2) ?></td>
                              <td>₦<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                    <!-- 
              <form method="POST">
                <input type="hidden" name="action" value="update_status">
                <div class="row">
                  
                    <?php if ($order['status'] == 'delivered' || $_SESSION['role'] == 'Delivery') { ?>

                    <?php } else { ?>
                      <h6 class="mt-4">Update Order Status</h6>

                      <div class="col-md-6">
                        <select name="status" class="form-select">
                          <option value="pending" <?php if ($order['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                          <option value="processing" <?php if ($order['status'] == 'processing') echo 'selected'; ?>>Processing</option>
                          <option value="shipped" <?php if ($order['status'] == 'shipped') echo 'selected'; ?>>Shipped</option>
                          <option value="delivered" <?php if ($order['status'] == 'delivered') echo 'selected'; ?>>Delivered</option>
                          <option value="cancelled" <?php if ($order['status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                        </select>
                      </div>

                      <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">Update Status</button>
                      </div>
                    <?php } ?>
                  

                </div>
              </form> -->

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