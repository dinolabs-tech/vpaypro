<?php
include('models/customer_order_details.php');
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
                <li class="breadcrumb-item"><a href="customer_orders.php">My Orders</a></li>
                <li class="breadcrumb-item active">My Order Details</li>
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
                  <h6><strong>Delivery Address: </strong></h6>
                  <p><?= htmlspecialchars($order['delivery_address']) ?></p>
                </div>
                <div class="col-md-6">
                  <h6>Order Summary</h6>
                  <p><strong>Total Amount:</strong> ₦<?= number_format($order['total_amount'], 2) ?></p>
                  <p><strong>Status:</strong> <span id="order_status"><?= htmlspecialchars($order['status']) ?></span></p>
                  <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
                  <div id="delivery_code_container" style="display: none;">
                    <p><strong>Delivery Code:</strong> <span id="delivery_code"></span></p>
                  </div>
                </div>
                <?php if ($order['status'] == 'shipped'): ?>
                <div class="col-md-12 mt-3">
                  <button id="confirm_delivery_btn" class="btn btn-primary" data-order-id="<?= htmlspecialchars($order['id']) ?>">Confirm Delivery</button>
                </div>
                <?php endif; ?>
                <div class="col-md-6">
                  <h6><strong>Delivery Personnel</strong></h6>
                  <?php if (!empty($order['delivery_person_name'])): ?>
                    <p><strong>Name:</strong> <?= htmlspecialchars($order['delivery_person_name']) ?></p>
                    <p><strong>Mobile:</strong> <?= htmlspecialchars($order['mobile']) ?></p>
                  <?php else: ?>
                    <p>Not yet assigned.</p>
                  <?php endif; ?>
                </div>
              </div>

              <h6 class="mt-4">Order Items</h6>
              <div class="table-responsive">

              <table class="table" id="basic-datatables">
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
      const confirmDeliveryBtn = document.getElementById('confirm_delivery_btn');
      if (confirmDeliveryBtn) {
        confirmDeliveryBtn.addEventListener('click', function() {
          const orderId = this.getAttribute('data-order-id');
          
          fetch('ajax/generate_delivery_code.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'order_id=' + orderId,
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              document.getElementById('order_status').textContent = 'Delivered';
              document.getElementById('delivery_code').textContent = data.delivery_code;
              document.getElementById('delivery_code_container').style.display = 'block';
              confirmDeliveryBtn.style.display = 'none';
            } else {
              alert(data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
          });
        });
      }
    });
  </script>
</body>

</html>