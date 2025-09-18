<?php
// audit_trails.php

include('models/audit_logs.php');
include('./database/db_connection.php'); // Ensure db connection is included

// Initialize AuditLog model
$auditLogModel = new AuditLog($conn);

// Handle filters
$logs = null; // Initialize logs to null
$noLogsFoundMessage = ''; // Initialize message for no logs found

if (isset($_GET['user_id_filter']) && !empty($_GET['user_id_filter'])) {
  $identifier = $_GET['user_id_filter'];
  $logs = $auditLogModel->getLogsByIdentifier($identifier);
  if ($logs !== null && $logs->num_rows === 0) {
    $noLogsFoundMessage = "No audit logs found for identifier: " . htmlspecialchars($identifier);
  }
} elseif (isset($_GET['start_date']) && !empty($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['end_date'])) {
  $startDate = $_GET['start_date'];
  $endDate = $_GET['end_date'];
  $logs = $auditLogModel->getLogsByDateRange($startDate, $endDate);
} else {
  // Default to showing all logs if no filters are applied
  $logs = $auditLogModel->getAllLogs();
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
            <h3 class="fw-bold mb-3">Audit Trails</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Audit Trails</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-3">
                      <div class="col-md-4">
                        <form method="GET" action="audit_trails.php">
                          <h5 class="card-title">Filter by User ID</h5>
                          <div class="input-group">
                            <input type="text" name="user_id_filter" class="form-control" placeholder="Enter Username, Customer ID, or User ID" value="<?= isset($_GET['user_id_filter']) ? htmlspecialchars($_GET['user_id_filter']) : '' ?>">
                            <button type="submit" class="btn btn-primary">Filter</button>
                          </div>
                        </form>
                      </div>
                      <div class="col-md-8">
                        <form method="GET" action="audit_trails.php">
                          <h5 class="card-title">Filter by Date Range</h5>
                          <div class="row">
                            <div class="col-md-5">
                              <input type="date" name="start_date" class="form-control" value="<?= isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : '' ?>">
                            </div>
                            <div class="col-md-5">
                              <input type="date" name="end_date" class="form-control" value="<?= isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : '' ?>">
                            </div>
                            <div class="col-md-2">
                              <button type="submit" class="btn btn-primary rounded">Filter</button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
                    <!-- End Filter Section -->

                    <h5 class="card-title">Administrative Actions Log</h5>

                    <?php if (!empty($noLogsFoundMessage)): ?>
                      <p><?= $noLogsFoundMessage ?></p>
                    <?php elseif ($logs && $logs->num_rows > 0): ?>
                      <div class="table-responsive">
                      <table class="table datatable" id="basic-datatables">
                        <thead>
                          <tr>
                            <th scope="col">S/N</th>
                            <th scope="col">User ID</th>
                            <th scope="col">Action</th>
                            <th scope="col">Details</th>
                            <th scope="col">Timestamp</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php while ($row = $logs->fetch_assoc()): ?>
                            <tr>
                              <th scope="row"><?= htmlspecialchars($row['log_id']) ?></th>
                              <td><?= htmlspecialchars($row['staffname']) ?></td>
                              <td><?= htmlspecialchars($row['action']) ?></td>
                              <td><?= htmlspecialchars($row['details']) ?></td>
                              <td><?= htmlspecialchars($row['timestamp']) ?></td>
                            </tr>
                          <?php endwhile; ?>
                        </tbody>
                      </table>
                      </div>
                    <?php else: ?>
                      <p>No audit logs found.</p>
                    <?php endif; ?>

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

  <?php
// Example of logging an action (this would typically be done in other parts of the application)
// if (isset($_SESSION['user_id'])) {
//     $auditLogModel->logAction($_SESSION['user_id'], 'Viewed Audit Trails');
// }
?>
</body>

</html>