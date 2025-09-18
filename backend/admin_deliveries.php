<?php
include('models/admin_deliveries.php');
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
            <h3 class="fw-bold mb-3">Manage Deliveries</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Deliveries</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">All Deliveries</h5>
                    <div class="table-responsive">

                      <!-- Table with stripped rows -->
                      <table class="table datatable" id="basic-datatables">
                        <thead>
                          <tr>
                            <th scope="col">Order ID</th>
                            <th scope="col">Status</th>
                            <th scope="col">Delivery Personnel</th>
                            <th scope="col">Estimated Delivery</th>
                            <th scope="col">Actual Delivery</th>
                            <th scope="col">Delivery Code</th>
                            <th scope="col">Country</th>
                            <th scope="col">State</th>
                            <th scope="col">Action</th>

                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($deliveries as $delivery): ?>
                            <tr>
                              <th scope="row"><?= $delivery['order_id'] ?></th>
                              <td><?= htmlspecialchars($delivery['status'] ?? '') ?></td>
                              <td><?= htmlspecialchars($delivery['delivery_personnel'] ?? '') ?></td>
                              <td><?= htmlspecialchars($delivery['estimated_delivery_date'] ?? '') ?></td>
                              <td><?= htmlspecialchars($delivery['actual_delivery_date'] ?? '') ?></td>
                              <td><?= htmlspecialchars($delivery['delivery_code'] ?? '') ?></td>
                              <td><?= htmlspecialchars($delivery['country'] ?? '') ?></td>
                              <td><?= htmlspecialchars($delivery['state'] ?? '') ?></td>

                              <?php if ($delivery['status'] == 'delivered') { ?>

                              <?php } else { ?>
                                <td>
                                  <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateDeliveryModal"
                                    data-id="<?= $delivery['id'] ?>"
                                    data-status="<?= $delivery['status'] ?>"
                                    data-personnel="<?= $delivery['delivery_personnel'] ?>"
                                    data-estimated_date="<?= $delivery['estimated_delivery_date'] ?>">
                                    Update
                                  </button>
                                </td>
                              <?php } ?>


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

          <div class="modal fade" id="updateDeliveryModal" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title text-dark">Update Delivery</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form method="POST" id="updateDeliveryForm">
                    <input type="hidden" name="action" value="update_delivery">
                    <input type="hidden" name="delivery_id" id="delivery_id">
                    <div class="mb-3">
                      <label for="status" class="form-label text-black">Status</label>
                      <select name="status" id="status" class="form-select form-control">
                        <option value="" selected disabled>Select Status</option>
                        <option value="pending">Pending</option>
                        <option value="shipped">Shipped</option>
                        <!-- <option value="delivered">Delivered</option> -->
                        <option value="failed">Failed</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="delivery_personnel" class="form-label text-black">Delivery Personnel</label>
                      <input type="text" name="delivery_personnel" id="delivery_personnel" class="form-control" value="<?php echo htmlspecialchars($_SESSION['staffname']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                      <label for="estimated_delivery_date" class="form-label text-black">Estimated Delivery Date</label>
                      <input type="date" name="estimated_delivery_date" id="estimated_delivery_date" class="form-control">
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" form="updateDeliveryForm" class="btn btn-primary">Save Changes</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php include('components/footer.php'); ?>
    </div>
  </div>
  <?php include('components/script.php'); ?>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var updateDeliveryModal = document.getElementById('updateDeliveryModal');
      updateDeliveryModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var deliveryId = button.getAttribute('data-id');
        var status = button.getAttribute('data-status');
        var personnel = button.getAttribute('data-personnel');
        var estimatedDate = button.getAttribute('data-estimated_date');

        var modalTitle = updateDeliveryModal.querySelector('.modal-title');
        var deliveryIdInput = updateDeliveryModal.querySelector('#delivery_id');
        var statusInput = updateDeliveryModal.querySelector('#status');
        var personnelInput = updateDeliveryModal.querySelector('#delivery_personnel');
        var estimatedDateInput = updateDeliveryModal.querySelector('#estimated_delivery_date');

        modalTitle.textContent = 'Update Delivery for Order #' + deliveryId;
        deliveryIdInput.value = deliveryId;
        statusInput.value = status;
        if (personnel) {
          personnelInput.value = personnel;
        }
        estimatedDateInput.value = estimatedDate;
      });
    });
  </script>
</body>

</html>