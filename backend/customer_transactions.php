<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'database/db_connection.php';
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['loggedin'])) {
  header('Location: index.php');
  exit;
}

// Fetch transaction history
// Fetch branch_id, country, state, and role for the logged-in user
$user_branch_id = null;
$user_country = null;
$user_state = null;
$user_role = $_SESSION['role'] ?? null;

if (isset($_SESSION['user_id'])) {
  $stmt = $conn->prepare("SELECT l.branch_id, b.country, b.state, l.role FROM login l LEFT JOIN branches b ON l.branch_id = b.branch_id WHERE l.id = ?");
  $stmt->bind_param("i", $_SESSION['user_id']);
  $stmt->execute();
  $result = $stmt->get_result();
  $user_data = $result->fetch_assoc();
  if ($user_data) {
    $user_branch_id = $user_data['branch_id'];
    $user_country = $user_data['country'];
    $user_state = $user_data['state'];
    $user_role = $user_data['role']; // Ensure role is up-to-date from DB
  }
  $stmt->close();
}

// Fetch transaction history, including the user who processed it
$sql = '
    SELECT
        ct.transaction_type,
        ct.amount,
        ct.created_at,
        l.staffname AS processed_by_staffname,
        b.branch_name AS branch_name,
        b.country AS branch_country,
        b.state AS branch_state
    FROM
        customer_transactions ct
    LEFT JOIN
        login l ON ct.processed_by_user_id = l.id
    LEFT JOIN
        branches b ON l.branch_id = b.branch_id
    WHERE
        ct.customer_id = ?
';

$params = ['s', $_SESSION['customer_id']]; // Initial parameters for customer_id

if ($user_role !== 'Superuser' && $user_role !== 'CEO') {
  if ($user_country !== null) {
    $sql .= ' AND b.country = ?';
    $params[0] .= 's';
    $params[] = $user_country;
  }
  if ($user_state !== null) {
    $sql .= ' AND b.state = ?';
    $params[0] .= 's';
    $params[] = $user_state;
  }
}

$sql .= ' ORDER BY ct.created_at DESC';

$stmt = $conn->prepare($sql);
if ($stmt === false) {
  die("Prepare failed: " . $conn->error);
}

// Dynamically bind parameters
$bind_params = [];
$bind_params[] = $params[0]; // The type string
for ($i = 1; $i < count($params); $i++) {
  $bind_params[] = &$params[$i]; // Pass subsequent parameters by reference
}
call_user_func_array([$stmt, 'bind_param'], $bind_params);

$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
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
            <h3 class="fw-bold mb-3">Customer Transactions</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="customer_dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Transactions</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-12">

                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">All Transactions</h5>
                    <div class="table-responsive">
                      <table class="table datatable" id="basic-datatables">
                        <thead>
                          <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Type</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Processed By</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($transactions as $transaction): ?>
                            <tr>
                              <td><?php echo $transaction['created_at']; ?></td>
                              <td><?php echo ucfirst($transaction['transaction_type']); ?></td>
                              <td>₦<?php echo number_format($transaction['amount'], 2); ?></td>
                              <td><?php echo htmlspecialchars($transaction['processed_by_staffname'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
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
</body>

</html>