<?php

include 'database/db_connection.php';
session_start();


// Redirect to login if user is not logged in
if ($_SESSION['role'] != 'Supplier') {
  header('Location: index.php');
  exit;
}

$order_type = 'Pending';
// Fetch Supplier Pending orders
$stmt = $conn->prepare('SELECT count(*) FROM purchase_orders WHERE supplier_id = ? AND status=?');
$stmt->bind_param('is', $_SESSION['user_id'], $order_type);
$stmt->execute();
$stmt->bind_result($Pending_orders);
$stmt->fetch();
$stmt->close();


$order_type = 'Accepted';
// Fetch Supplier Accepted orders
$stmt = $conn->prepare('SELECT count(*) FROM purchase_orders WHERE supplier_id = ? AND status=?');
$stmt->bind_param('is', $_SESSION['user_id'], $order_type);
$stmt->execute();
$stmt->bind_result($accepted_orders);
$stmt->fetch();
$stmt->close();

$order_type = 'In-Transit';
// Fetch Supplier Orders-in-Transit orders
$stmt = $conn->prepare('SELECT count(*) FROM purchase_orders WHERE supplier_id = ? AND status=?');
$stmt->bind_param('is', $_SESSION['user_id'], $order_type);
$stmt->execute();
$stmt->bind_result($transit_orders);
$stmt->fetch();
$stmt->close();

$order_type = 'Received';
// Fetch Supplier Orders Received by the Company
$stmt = $conn->prepare('SELECT count(*) FROM purchase_orders WHERE supplier_id = ? AND status=?');
$stmt->bind_param('is', $_SESSION['user_id'], $order_type);
$stmt->execute();
$stmt->bind_result($completed_orders);
$stmt->fetch();
$stmt->close();

// TOTAL ORDER BALANCES

$order_type = 'Pending';
// Fetch Supplier Pending orders
$stmt = $conn->prepare('SELECT SUM(total_amount) FROM purchase_orders WHERE supplier_id = ? AND status=?');
$stmt->bind_param('is', $_SESSION['user_id'], $order_type);
$stmt->execute();
$stmt->bind_result($total_Pending_orders);
$stmt->fetch();
$stmt->close();


$order_type = 'Accepted';
// Fetch Supplier Accepted orders
$stmt = $conn->prepare('SELECT SUM(total_amount) FROM purchase_orders WHERE supplier_id = ? AND status=?');
$stmt->bind_param('is', $_SESSION['user_id'], $order_type);
$stmt->execute();
$stmt->bind_result($total_accepted_orders);
$stmt->fetch();
$stmt->close();


$order_type = 'In-Transit';
// Fetch Supplier Orders-in-Transit orders
$stmt = $conn->prepare('SELECT SUM(total_amount) FROM purchase_orders WHERE supplier_id = ? AND status=?');
$stmt->bind_param('is', $_SESSION['user_id'], $order_type);
$stmt->execute();
$stmt->bind_result($total_transit_orders);
$stmt->fetch();
$stmt->close();

$order_type = 'Received';
// Fetch Supplier Orders Received by the Company
$stmt = $conn->prepare('SELECT SUM(total_amount) FROM purchase_orders WHERE supplier_id = ? AND status=?');
$stmt->bind_param('is', $_SESSION['user_id'], $order_type);
$stmt->execute();
$stmt->bind_result($total_completed_orders);
$stmt->fetch();
$stmt->close();

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
            <h3 class="fw-bold mb-3">Supplier Dashboard <?php echo $_SESSION['role']?></h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="supplier_dashboard.php">Home</a></li>
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
              <div class="col-xl-3">
                <div class="card">
                  <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                    <h2><?= $Pending_orders ?></h2>
                    <h3>Pending Orders</h3>
                    <h3></h3>
                  </div>
                </div>
              </div>

              <div class="col-xl-3">
                <div class="card">
                  <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                    <h2><?= $accepted_orders ?></h2>
                    <h3>Accepted Orders</h3>
                    <h3></h3>
                  </div>
                </div>
              </div>

              <div class="col-xl-3">
                <div class="card">
                  <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                    <h2><?= $transit_orders ?></h2>
                    <h3>Orders in Transit</h3>
                    <h3></h3>
                  </div>
                </div>
              </div>

              <div class="col-xl-3">
                <div class="card">
                  <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                    <h2><?= $completed_orders ?></h2>
                    <h3>Completed Orders</h3>
                    <h3></h3>
                  </div>
                </div>
              </div>

              <div class="col-xl-3">
                <div class="card">
                  <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                    <h2><?= number_format((float)($total_Pending_orders ?? 0), 2); ?></h2>

                    <h3>Total Pending Orders</h3>
                    <h3></h3>
                  </div>
                </div>
              </div>

              <div class="col-xl-3">
                <div class="card">
                  <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                    <h2><?= number_format((float)($total_accepted_orders ?? 0), 2); ?></h2>
                    <h3>Total Accepted Orders</h3>
                    <h3></h3>
                  </div>
                </div>
              </div>

              <div class="col-xl-3">
                <div class="card">
                  <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                    <h2><?= number_format((float)($total_transit_orders ?? 0), 2); ?></h2>
                    <h3>Total Orders in Transit</h3>
                    <h3></h3>
                  </div>
                </div>
              </div>

              <div class="col-xl-3">
                <div class="card">
                  <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                    <h2><?= number_format((float)($total_completed_orders ?? 0), 2); ?></h2>
                    <h3>Total Completed Orders</h3>
                    <h3></h3>
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