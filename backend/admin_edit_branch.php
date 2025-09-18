<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection and Branch model
include './database/db_connection.php';
include './models/branches.php';
session_start();


// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

$branchModel = new Branch($conn);
$message = '';
$branch = null;

// Get branch ID from URL
if (isset($_GET['id'])) {
  $branchId = $_GET['id'];
  $branch = $branchModel->getBranchById($branchId);
  if (!$branch) {
    // Redirect if branch not found
    header("Location: admin_branches.php?msg=notfound");
    exit();
  }
} else {
  // Redirect if no ID is provided
  header("Location: admin_branches.php");
  exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $branchName = trim($_POST['branch_name'] ?? '');
  $country = trim($_POST['country'] ?? '');
  $state = trim($_POST['state'] ?? '');
  $branchId = $_POST['branch_id'];

  if ($branchName === '' || $country === '' || $state === '') {
    $message = "All fields are required.";
  } else {
    if ($branchModel->updateBranch($branchId, $branchName, $country, $state)) {
      // Redirect to branch list page after successful update
      header("Location: admin_branches.php?msg=updated");
      exit();
    } else {
      $message = "Error updating branch. Please try again.";
    }
  }
}
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
            <h3 class="fw-bold mb-3">Branch Management</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item"><a href="admin_branches.php">Manage Branches</a></li>
                <li class="breadcrumb-item active">Edit Branch</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Edit Branch</h5>

                    <?php if ($message): ?>
                      <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>

                    <form method="POST" class="row g-3">
                      <input type="hidden" name="branch_id" value="<?= htmlspecialchars($branch['branch_id']) ?>">
                      <div class="col-md-6">
                        <label for="branch_name" class="form-label">Branch Name</label>
                        <input type="text" class="form-control" id="branch_name" name="branch_name" value="<?= htmlspecialchars($branch['branch_name']) ?>" required>
                      </div>

                      <div class="col-md-6">
                        <label for="country" class="form-label">Country</label>
                        <select class="form-select" id="country" name="country" required>
                          <option value="">Select Country</option>
                          <option value="<?= htmlspecialchars($branch['country']) ?>" selected><?= htmlspecialchars($branch['country']) ?></option>
                        </select>
                      </div>

                      <div class="col-md-6">
                        <label for="state" class="form-label">State</label>
                        <select class="form-select" id="state" name="state" required>
                          <option value="">Select State</option>
                          <option value="<?= htmlspecialchars($branch['state']) ?>" selected><?= htmlspecialchars($branch['state']) ?></option>
                        </select>
                      </div>

                      <div class="col-12">
                        <button type="submit" class="btn btn-primary rounded"><i class="fas fa-save"></i></button>
                        <a href="admin_branches.php" class="btn btn-secondary rounded"><i class="fas fa-window-close"></i></a>
                      </div>
                    </form>

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

  <script src="assets/js/country_state_selector.js"></script>
  <script>
    // Pre-select the state after the states have been loaded
    document.addEventListener('DOMContentLoaded', function() {
      const stateSelect = document.getElementById('state');
      const countrySelect = document.getElementById('country');

      const observer = new MutationObserver(function(mutations) {
        if (stateSelect.options.length > 1) {
          stateSelect.value = "<?= htmlspecialchars($branch['state']) ?>";
          observer.disconnect(); // Stop observing once the state is set
        }
      });

      observer.observe(stateSelect, {
        childList: true
      });

      // Trigger state loading
      countrySelect.dispatchEvent(new Event('change'));
    });
  </script>
</body>

</html>