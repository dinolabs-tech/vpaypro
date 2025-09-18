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


$customer_details = null;
$error = null;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['customer_id'])) {
  $customer_id = $_POST['customer_id'];

  $sql = "SELECT name, email, balance, profile_picture FROM customers WHERE customer_id = ?";

  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
      $customer_details = $result->fetch_assoc();
    } else {
      $error = "Invalid Customer ID.";
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
            <h3 class="fw-bold mb-3">Check Customer Balance</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Check Balance</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-6">

                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Enter Customer ID</h5>

                    <form method="POST">
                      <div class="row mb-3">
                        <label for="customer_id" class="col-sm-4 col-form-label">Customer ID</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control" name="customer_id" id="customer_id" required>
                        </div>
                      </div>
                      <div class="row mb-3">
                        <div class="col-sm-8 offset-sm-4">
                          <button type="submit" class="btn btn-primary rounded">Check Balance</button>
                        </div>
                      </div>
                    </form>

                  </div>
                </div>

              </div>

              <?php if ($customer_details): ?>
                <div class="col-lg-6">
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">Customer Details</h5>
                      <div class="text-center">
                        <img src="assets/img/<?php echo htmlspecialchars($customer_details['profile_picture']); ?>" alt="Profile" class="rounded-circle" style="width: 150px;height:150px;">
                      </div>
                      <div class="table-responsive">
                      <table class="table table-bordered mt-3">
                        <tbody>
                          <tr>
                            <th scope="row">Name</th>
                            <td><?php echo htmlspecialchars($customer_details['name']); ?></td>
                          </tr>
                          <tr>
                            <th scope="row">Email</th>
                            <td><?php echo htmlspecialchars($customer_details['email']); ?></td>
                          </tr>
                          <tr>
                            <th scope="row">Balance</th>
                            <td>₦<?php echo number_format($customer_details['balance'], 2); ?></td>
                          </tr>
                        </tbody>
                      </table>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <?php if ($error): ?>
                <div class="col-lg-6">
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