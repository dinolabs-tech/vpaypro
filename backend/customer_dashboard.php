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


// Fetch customer data
$stmt = $conn->prepare('SELECT name, balance, profile_picture FROM customers WHERE id = ?');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($name, $balance, $profile_picture);
$stmt->fetch();
$stmt->close();

// Fetch transaction history
$stmt = $conn->prepare('SELECT transaction_type, amount, created_at FROM customer_transactions WHERE customer_id = ? ORDER BY created_at DESC');
$stmt->bind_param('s', $_SESSION['customer_id']);
$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle top-up form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['topup_amount'])) {
  $topup_amount = $_POST['topup_amount'];

  // Update customer balance
  $new_balance = $balance + $topup_amount;
  $stmt = $conn->prepare('UPDATE customers SET balance = ? WHERE id = ?');
  $stmt->bind_param('di', $new_balance, $_SESSION['user_id']);
  $stmt->execute();
  $stmt->close();

  // Record transaction
  $stmt = $conn->prepare('INSERT INTO customer_transactions (customer_id, transaction_type, amount) VALUES (?, ?, ?)');
  $transaction_type = 'funding';
  $stmt->bind_param('ssd', $_SESSION['customer_id'], $transaction_type, $topup_amount);
  $stmt->execute();
  $stmt->close();

  // Refresh page to show new balance
  header('Location: customer_dashboard.php');
  exit;
}
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
            <h3 class="fw-bold mb-3">Customer Dashboard</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
              </ol>
            </nav>
          </div>

          <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?php echo $_SESSION['success_message']; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
          <?php endif; ?>

          <section class="section profile">
            <div class="row">
              <div class="col-xl-4">

                <div class="card">
                  <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

                    <img src="assets/img/<?php echo $profile_picture; ?>" alt="Profile" class="rounded-circle" style="height: 120px; width:120px;">
                    <h2><?php echo $name; ?></h2><br>
                    <h3 class="text-center fw-bolder">Customer ID <br><?php echo $_SESSION['customer_id']; ?></h3>
                    <br>
                    <h2><i class="bi bi-cash"></i> ₦<?php echo number_format((float)($balance ?? 0), 2); ?></h2>
                  </div>
                </div>

              </div>

              <div class="col-xl-8">

                <div class="card">
                  <div class="card-body pt-3">
                    <!-- Bordered Tabs -->
                    <ul class="nav nav-tabs nav-tabs-bordered">

                      <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                      </li>

                      <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Top-Up</button>
                      </li>

                    </ul>
                    <div class="tab-content pt-2">

                      <div class="tab-pane fade show active profile-overview" id="profile-overview">
                        <h5 class="card-title">Transaction History</h5>
                        <div class="table-responsive">
                          <table class="table datatable" id="basic-datatables">
                            <thead>
                              <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Type</th>
                                <th scope="col">Amount</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                  <td><?php echo $transaction['created_at']; ?></td>
                                  <td><?php echo ucfirst($transaction['transaction_type']); ?></td>
                                  <td>₦<?php echo number_format($transaction['amount'], 2); ?></td>
                                </tr>
                              <?php endforeach; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>

                      <div class="tab-pane fade pt-3" id="profile-edit">

                        <!-- Top-up Form -->
                        <form method="POST" action="api/flutterwave.php">
                          <input type="hidden" name="amount" id="topup_amount_hidden">
                          <div class="row mb-3 justify-content-center">
                            <div class="col-md-8 col-lg-9">
                              <input type="text" class="d-none" name="customer_id" value="<?php echo $_SESSION['customer_id']; ?>">
                              <input name="topup_amount_input" type="number" class="form-control" id="topup_amount" required oninput="document.getElementById('topup_amount_hidden').value = this.value;" placeholder="Enter Amount">
                            </div>
                          </div>

                          <div class="text-center">
                            <button type="submit" class="btn btn-primary rounded">Top-Up</button>
                          </div>
                        </form><!-- End Top-up Form -->

                      </div>

                    </div><!-- End Bordered Tabs -->

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