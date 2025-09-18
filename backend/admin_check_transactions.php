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


$transactions = [];
$error = null;
$customer_id = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['customer_id'])) {
  $customer_id = $_POST['customer_id'];

  $sql = "SELECT ct.*, l.staffname FROM customer_transactions ct
  INNER JOIN login l ON ct.processed_by_user_id = l.id
  WHERE customer_id = ? ORDER BY created_at DESC";

  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $transactions = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $error = "No transactions found for this Customer ID.";
    }
    $stmt->close();
  } else {
    $error = "Error: " . $conn->error;
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
            <h3 class="fw-bold mb-3">Check Customer Transactions</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Check Transactions</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-12">

                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Enter Customer ID</h5>

                    <form method="POST">
                      <div class="row mb-3">
                        <label for="customer_id" class="col-sm-2 col-form-label">Customer ID</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="customer_id" id="customer_id" value="<?php echo htmlspecialchars($customer_id); ?>" required>
                        </div>
                      </div>
                      <div class="row mb-3">
                        <div class="col-sm-10 offset-sm-2">
                          <button type="submit" class="btn btn-primary rounded">Check Transactions</button>
                        </div>
                      </div>
                    </form>

                  </div>
                </div>

              </div>

              <?php if (!empty($transactions)): ?>
                <div class="col-lg-12">
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">Transaction History for <?php echo htmlspecialchars($customer_id); ?></h5>
                      <div class="table-responsive">
                        <table class="table datatable" id="basic-datatables">
                          <thead>
                            <tr>
                              <th scope="col">Date</th>
                              <th scope="col">Type</th>
                              <th scope="col">Amount</th>
                              <th scope="col">Staff</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($transactions as $transaction): ?>
                              <tr>
                                <td><?php echo $transaction['created_at']; ?></td>
                                <td><?php echo ucfirst($transaction['transaction_type']); ?></td>
                                <td>₦<?php echo number_format($transaction['amount'], 2); ?></td>
                                <td><?php echo $transaction['staffname']; ?></td>

                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <?php if ($error): ?>
                <div class="col-lg-12">
                  <div class="alert alert-danger"><?php echo $error; ?></div>
                </div>
              <?php endif; ?>

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