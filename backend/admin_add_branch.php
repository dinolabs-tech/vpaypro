<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'database/db_connection.php';
session_start();


// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

// Include Branch model
include './models/branches.php';

$branchModel = new Branch($conn);
$message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $branchName = trim($_POST['branch_name'] ?? '');
  $country = trim($_POST['country'] ?? '');
  $state = trim($_POST['state'] ?? '');

  if ($branchName === '' || $country === '' || $state === '') {
    $message = "All fields are required.";
  } else {
    if ($branchModel->createBranch($branchName, $country, $state)) {
      // Redirect to branch list page after successful creation
      header("Location: admin_branches.php?msg=added");
      exit();
    } else {
      $message = "Error creating branch. Please try again.";
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
                <li class="breadcrumb-item active">Add New Branch</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Add New Branch</h5>

                    <?php if ($message): ?>
                      <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>

                    <form method="POST" class="row g-3">
                      <div class="col-md-6">
                        <label for="branch_name" class="form-label">Branch Name</label>
                        <input type="text" class="form-control" id="branch_name" name="branch_name" required>
                      </div>

                      <div class="col-md-6">
                        <label for="country" class="form-label">Country</label>
                        <select class="form-select" id="country" name="country" required>
                          <option value="">Select Country</option>
                        </select>
                      </div>

                      <div class="col-md-6">
                        <label for="state" class="form-label">State</label>
                        <select class="form-select" id="state" name="state" required disabled>
                          <option value="">Select State</option>
                        </select>
                      </div>

                      <div class="col-12">
                        <button type="submit" class="btn btn-primary">Add Branch</button>
                        <a href="admin_branches.php" class="btn btn-secondary">Cancel</a>
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
</body>

</html>