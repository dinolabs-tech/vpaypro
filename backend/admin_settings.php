<?php
include('models/admin_settings.php');
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
            <h3 class="fw-bold mb-3">Settings</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Settings</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-6">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">General Settings</h5>

                    <?php if ($message): ?>
                      <div class="alert alert-info">
                        <?= htmlspecialchars($message) ?>
                      </div>
                    <?php endif; ?>

                    <form method="POST">
                      <input type="hidden" name="action" value="update_settings">
                      <div class="row mb-3">
                        <label for="delivery_fee" class="col-sm-4 col-form-label">Delivery Fee (₦)</label>
                        <div class="col-sm-8">
                          <input type="number" class="form-control" id="delivery_fee" name="delivery_fee" value="<?= htmlspecialchars($delivery_fee) ?>" required>
                        </div>
                      </div>

                      <div class="row mb-3">
                        <label for="currency" class="col-sm-4 col-form-label">Primary Currency</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control" id="currency" name="currency" value="<?= htmlspecialchars($currency) ?>" required>
                        </div>
                      </div>

                      <div class="row mb-3">
                        <label for="language" class="col-sm-4 col-form-label">Default Language</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control" id="language" name="language" value="<?= htmlspecialchars($language) ?>" required>
                        </div>
                      </div>

                      <div class="text-center">
                        <button type="submit" class="btn btn-primary rounded"><i class="fas fa-save"></i></button>
                      </div>
                    </form>

                  </div>
                </div>
              </div>

              <!-- Links to other settings pages -->
              <div class="col-lg-6">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Other Settings</h5>
                    <ul class="list-group list-group-flush">
                      <li class="list-group-item">
                        <a href="audit_trails.php" class="text-primary">Audit Trails</a>
                      </li>
                      <!-- <li class="list-group-item">
                  <a href="email_notifications.php" class="text-primary">Email Notification Settings</a>
                </li> -->
                      <li class="list-group-item">
                        <a href="payment_gateways.php" class="text-primary">Payment Gateway Integration</a>
                      </li>
                      <li class="list-group-item">
                        <a href="tax_settings.php" class="text-primary">Tax Settings</a>
                      </li>
                    </ul>
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