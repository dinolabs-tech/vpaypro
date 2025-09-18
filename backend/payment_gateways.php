<?php
// payment_gateways.php

include('models/payment_gateways.php');
include('./database/db_connection.php'); // Ensure db connection is included

// Initialize PaymentGateway model
$paymentGatewayModel = new PaymentGateway($conn);

$message = '';
$gateways = [];

// Handle form submission for adding/editing gateways
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['action']) && $_POST['action'] === 'add_gateway') {
    $name = $_POST['gateway_name'];
    $apiKey = $_POST['api_key'];
    $apiSecret = $_POST['api_secret'];
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if ($paymentGatewayModel->createGateway($name, $apiKey, $apiSecret, $isActive)) {
      $message = "Payment gateway added successfully.";
    } else {
      $message = "Failed to add payment gateway.";
    }
  } elseif (isset($_POST['action']) && $_POST['action'] === 'edit_gateway') {
    $gatewayId = $_POST['gateway_id'];
    $name = $_POST['gateway_name'];
    $apiKey = $_POST['api_key'];
    $apiSecret = $_POST['api_secret'];
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if ($paymentGatewayModel->updateGateway($gatewayId, $name, $apiKey, $apiSecret, $isActive)) {
      $message = "Payment gateway updated successfully.";
    } else {
      $message = "Failed to update payment gateway.";
    }
  } elseif (isset($_POST['action']) && $_POST['action'] === 'delete_gateway') {
    $gatewayId = $_POST['gateway_id'];
    if ($paymentGatewayModel->deleteGateway($gatewayId)) {
      $message = "Payment gateway deleted successfully.";
    } else {
      $message = "Failed to delete payment gateway.";
    }
  }
}

// Fetch all gateways for display
$gateways = $paymentGatewayModel->getAllGateways();

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
            <h3 class="fw-bold mb-3">Payment Gateway Settings</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Payment Gateway</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Payment Gateways</h5>

                    <?php if ($message): ?>
                      <div class="alert alert-info">
                        <?= htmlspecialchars($message) ?>
                      </div>
                    <?php endif; ?>

                    <!-- Add New Gateway Form -->
                    <button type="button" class="btn btn-primary mb-3 rounded" data-bs-toggle="modal" data-bs-target="#addGatewayModal">
                    <i class="fas fa-plus"></i></button>

                    <!-- Payment Gateways Table -->
                    <?php if ($gateways && $gateways->num_rows > 0): ?>
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th>Gateway Name</th>
                            <th>API Key</th>
                            <th>Status</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php while ($row = $gateways->fetch_assoc()): ?>
                            <tr>
                              <td><?= htmlspecialchars($row['gateway_name']) ?></td>
                              <td><?= htmlspecialchars($row['api_key']) ?></td>
                              <td><?= $row['is_active'] ? 'Active' : 'Inactive' ?></td>
                              <td>
                                <button type="button" class="btn btn-sm btn-info edit-btn rounded" data-bs-toggle="modal" data-bs-target="#editGatewayModal"
                                  data-id="<?= $row['gateway_id'] ?>"
                                  data-name="<?= htmlspecialchars($row['gateway_name']) ?>"
                                  data-apikey="<?= htmlspecialchars($row['api_key']) ?>"
                                  data-apisecret="<?= htmlspecialchars($row['api_secret']) ?>"
                                  data-isactive="<?= $row['is_active'] ?>">
                                  <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" style="display:inline-block;">
                                  <input type="hidden" name="action" value="delete_gateway">
                                  <input type="hidden" name="gateway_id" value="<?= $row['gateway_id'] ?>">
                                  <button type="submit" class="btn btn-sm btn-danger rounded"><i class="fas fa-trash"></i></button>
                                </form>
                              </td>
                            </tr>
                          <?php endwhile; ?>
                        </tbody>
                      </table>
                    <?php else: ?>
                      <p>No payment gateways found.</p>
                    <?php endif; ?>

                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Add Gateway Modal -->
          <div class="modal fade" id="addGatewayModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title text-black">Add New Payment Gateway</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form method="POST" id="addGatewayForm">
                    <input type="hidden" name="action" value="add_gateway">
                    <div class="mb-3">
                      <!-- <label for="gateway_name" class="form-label">Gateway Name</label> -->

                      <select name="gateway_name" id="gateway_name" class="form-select mt-2 form-control">
                        <option value="" selected disabled>Select Payment Gateway</option>
                        <option value="flutterwave">Flutterwave</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <input type="text" class="form-control" id="api_key" name="api_key" placeholder="API Key" required>
                    </div>
                    <div class="mb-3">
                      <input type="text" class="form-control" id="api_secret" name="api_secret" placeholder="API Secret Key" required>
                    </div>
                    <div class="mb-3 form-check">
                      <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1">
                      <label class="form-check-label text-black" for="is_active">Active</label>
                    </div>
                    <button type="submit" class="btn btn-primary rounded"><i class="fas fa-save"></i></button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit Gateway Modal -->
          <div class="modal fade" id="editGatewayModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title text-black">Edit Payment Gateway</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form method="POST" id="editGatewayForm">
                    <input type="hidden" name="action" value="edit_gateway">
                    <input type="hidden" name="gateway_id" id="edit_gateway_id">
                    <div class="mb-3">
                      <!-- <input type="text" class="form-control" id="edit_gateway_name" name="gateway_name" required> -->
                      <select name="gateway_name" id="edit_gateway_name" class="form-select mt-2 form-control">
                        <option value="" selected disabled>Select Payment Gateway</option>
                        <option value="flutterwave">Flutterwave</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="edit_api_key" class="form-label text-black">API Key</label>
                      <input type="text" class="form-control" id="edit_api_key" name="api_key" required>
                    </div>
                    <div class="mb-3">
                      <label for="edit_api_secret" class="form-label text-black">API Secret</label>
                      <input type="text" class="form-control" id="edit_api_secret" name="api_secret" required>
                    </div>
                    <div class="mb-3 form-check">
                      <input type="checkbox" class="form-check-input" id="edit_is_active" name="is_active" value="1">
                      <label class="form-check-label text-black" for="edit_is_active">Active</label>
                    </div>
                    <button type="submit" class="btn btn-primary rounded"><i class="fas fa-save"></i></button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <script>
            // JavaScript to populate the edit modal
            document.addEventListener('DOMContentLoaded', function() {
              var editButtons = document.querySelectorAll('.edit-btn');
              editButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                  var id = this.getAttribute('data-id');
                  var name = this.getAttribute('data-name');
                  var apiKey = this.getAttribute('data-apikey');
                  var apiSecret = this.getAttribute('data-apisecret');
                  var isActive = this.getAttribute('data-isactive');

                  document.getElementById('edit_gateway_id').value = id;
                  document.getElementById('edit_gateway_name').value = name;
                  document.getElementById('edit_api_key').value = apiKey;
                  document.getElementById('edit_api_secret').value = apiSecret;
                  var isActiveCheckbox = document.getElementById('edit_is_active');
                  isActiveCheckbox.checked = (isActive == 1);
                });
              });
            });
          </script>
        </div>
      </div>

      <?php include('components/footer.php'); ?>
    </div>
  </div>
  <?php include('components/script.php'); ?>
</body>

</html>