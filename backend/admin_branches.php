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

// Include database connection and Branch model
include './models/branches.php';

$branchModel = new Branch($conn);
$branches = $branchModel->getAllBranches();

$message = '';
if (isset($_GET['msg'])) {
  if ($_GET['msg'] == 'added') {
    $message = "Branch added successfully.";
  } elseif ($_GET['msg'] == 'updated') {
    $message = "Branch updated successfully.";
  } elseif ($_GET['msg'] == 'deleted') {
    $message = "Branch deleted successfully.";
  }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
  $branchId = $_GET['id'];
  if ($branchModel->deleteBranch($branchId)) {
    header("Location: admin_branches.php?msg=deleted");
    exit();
  } else {
    $message = "Error deleting branch. Please try again.";
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
                <li class="breadcrumb-item active">Branches</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Branches List</h5>

                    <?php if ($message): ?>
                      <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>

                    <a href="admin_add_branch.php" class="btn btn-primary mb-3 rounded"><i class="fas fa-plus"></i></a>

                    <div class="table-responsive">
                    <table class="table datatable" id="basic-datatables">
                      <thead>
                        <tr>
                          <th>Branch ID</th>
                          <th>Branch Name</th>
                          <th>State</th>
                          <th>Country</th>
                          
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (empty($branches)): ?>
                          <tr>
                            <td colspan="5" class="text-center">No branches found.</td>
                          </tr>
                        <?php else: ?>
                          <?php foreach ($branches as $branch): ?>
                            <tr>
                              <td><?= htmlspecialchars($branch['branch_id']) ?></td>
                              <td><?= htmlspecialchars($branch['branch_name']) ?></td>
                              <td><?= htmlspecialchars($branch['state']) ?></td>
                              <td><?= htmlspecialchars($branch['country']) ?></td>
                              
                              <td>
                                <a href="admin_edit_branch.php?id=<?= urlencode($branch['branch_id']) ?>" class="btn btn-sm btn-primary rounded"><i class="fas fa-edit"></i></a>
                                <a href="admin_branches.php?action=delete&id=<?= urlencode($branch['branch_id']) ?>" class="btn btn-sm btn-danger rounded" onclick="return confirm('Are you sure you want to delete this branch?');"><i class="fas fa-trash"></i></a>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php endif; ?>
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
</body>

</html>