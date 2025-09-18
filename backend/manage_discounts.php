<?php

include 'database/db_connection.php';
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}


if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$message = '';

// Handle discount update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $product_id = $_POST['product_id'];
  $discount = $_POST['discount'];

  $updateQuery = "UPDATE product SET discount = ? WHERE productid = ?";
  $stmt = $conn->prepare($updateQuery);
  $stmt->bind_param("ii", $discount, $product_id);

  if ($stmt->execute()) {
    $message = "Discount updated successfully for product ID: " . $product_id;
  } else {
    $message = "Error updating discount: " . $stmt->error;
  }
  $stmt->close();
}

// Fetch products
$productQuery = "SELECT productid, productname, sellprice, discount FROM product";
$result = $conn->query($productQuery);

if ($result === false) {
  die("Error fetching products: " . $conn->error);
}

$products = [];
while ($row = $result->fetch_assoc()) {
  $products[] = $row;
}

$conn->close();
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
            <h3 class="fw-bold mb-3">Manage Discounts</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Manage Product Discounts</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-12">

                <div class="card">
                  <div class="card-header">
                    <h5 class="card-title">Set Product Discounts</h5>
                  </div>
                  <div class="card-body">

                    <?php if ($message): ?>
                      <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>

                    <div class="table-responsive">
                      <table class="table table-bordered datatable" id="basic-datatables">
                        <thead>
                          <tr>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Current Discount</th>
                            <th>New Discount (%)</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($products as $product): ?>
                            <tr>
                              <td><?= htmlspecialchars($product['productname'], ENT_QUOTES, 'UTF-8') ?></td>
                              <td><?= number_format($product['sellprice'], 2) ?></td>
                              <td><?= htmlspecialchars($product['discount'], ENT_QUOTES, 'UTF-8') ?>%</td>
                              <td>
                                <form method="POST" class="d-inline">
                                  <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['productid'], ENT_QUOTES, 'UTF-8') ?>">
                                  <input type="number" name="discount" value="<?= htmlspecialchars($product['discount'], ENT_QUOTES, 'UTF-8') ?>" min="0" max="100" class="form-control w-100 d-inline">
                              </td>
                              <td>
                                <button type="submit" class="btn btn-primary btn-sm rounded"><i class="fas fa-save"></i></button>
                                </form>
                              </td>
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