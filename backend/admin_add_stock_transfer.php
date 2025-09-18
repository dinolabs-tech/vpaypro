<?php

include 'database/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

include('models/stock_transfers.php');
include('models/branches.php'); // Include the Branch model


$stockTransferModel = new StockTransfer($conn);
$branchModel = new Branch($conn); // Instantiate Branch model
$branches = $branchModel->getAllBranches(); // Get all branches

$message = '';
$products = []; // Initialize products array

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $from_branch_id = filter_var($_POST['from_branch_id'], FILTER_SANITIZE_NUMBER_INT);
  $to_branch_id = filter_var($_POST['to_branch_id'], FILTER_SANITIZE_NUMBER_INT);
  $product_id = filter_var($_POST['product_id'], FILTER_SANITIZE_NUMBER_INT);
  $quantity = filter_var($_POST['quantity'], FILTER_SANITIZE_NUMBER_INT);
  $notes = trim($_POST['notes'] ?? '');

  // Basic validation
  if ($from_branch_id == $to_branch_id) {
    $message = "Source and destination branches cannot be the same.";
  } elseif ($quantity <= 0) {
    $message = "Quantity must be a positive number.";
  } else {
    // Check if 'from' branch has enough stock for the selected product
    $product_stock_in_from_branch = $stockTransferModel->getProductStockInBranch($product_id, $from_branch_id);

    if ($product_stock_in_from_branch < $quantity) {
      $message = "Insufficient stock in the source branch for this product. Available: " . $product_stock_in_from_branch;
    } else {
      if ($stockTransferModel->createBranchStockTransfer($from_branch_id, $to_branch_id, $product_id, $quantity, $notes)) {
        header("Location: admin_stock_transfers.php?msg=added");
        exit();
      } else {
        $message = "Error recording stock transfer.";
      }
    }
  }
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
            <h3 class="fw-bold mb-3">Add Stock Transfer</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item"><a href="admin_stock_transfers.php">Manage Stock Transfers</a></li>
                <li class="breadcrumb-item active">Add New</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Stock Transfer Details</h5>

                    <?php if ($message): ?>
                      <div class="alert alert-danger">
                        <?= htmlspecialchars($message) ?>
                      </div>
                    <?php endif; ?>

                    <form method="POST" class="row g-3">
                      <div class="col-md-6">
                        <label for="from_branch_id" class="form-label">From Branch</label>
                        <select id="from_branch_id" name="from_branch_id" class="form-select" required>
                          <option value="">Select Source Branch</option>
                          <?php foreach ($branches as $branch): ?>
                            <option value="<?= $branch['branch_id'] ?>"><?= htmlspecialchars($branch['branch_name']) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label for="to_branch_id" class="form-label">To Branch</label>
                        <select id="to_branch_id" name="to_branch_id" class="form-select" required>
                          <option value="">Select Destination Branch</option>
                          <?php foreach ($branches as $branch): ?>
                            <option value="<?= $branch['branch_id'] ?>"><?= htmlspecialchars($branch['branch_name']) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label for="product_id" class="form-label">Product</label>
                        <select id="product_id" name="product_id" class="form-select" required>
                          <option value="">Select Product</option>
                          <!-- Products will be loaded dynamically via JavaScript -->
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required min="1">
                      </div>
                      <div class="col-md-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                      </div>

                      <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save "></i>Record Transfer</button>
                        <a href="admin_stock_transfers.php" class="btn btn-secondary rounded"><i class="fas fa-window-close">Cancel</i></a>
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

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const fromBranchSelect = document.getElementById('from_branch_id');
      const productSelect = document.getElementById('product_id');

      fromBranchSelect.addEventListener('change', function() {
        const branchId = this.value;
        productSelect.innerHTML = '<option value="">Loading Products...</option>'; // Clear and show loading

        if (branchId) {
          // Fetch products for the selected branch
          fetch(`ajax/get_products_by_branch.php?branch_id=${branchId}`)
            .then(async response => {
              const isJson = response.headers.get('content-type')?.includes('application/json');
              const data = isJson ? await response.json() : null;

              if (!response.ok) {
                const error = (data && data.error) || response.statusText;
                throw new Error(error);
              }
              return data;
            })
            .then(data => {
              productSelect.innerHTML = '<option value="">Select Product</option>'; // Reset
              if (data.products && data.products.length > 0) {
                data.products.forEach(product => {
                  const option = document.createElement('option');
                  option.value = product.productid;
                  option.textContent = `${product.productname} (Qty: ${product.qty})`;
                  productSelect.appendChild(option);
                });
              } else {
                productSelect.innerHTML = '<option value="">No products found for this branch</option>';
              }
            })
            .catch(error => {
              console.error('Error fetching products:', error);
              productSelect.innerHTML = `<option value="">Error: ${error.message}</option>`;
            });
        } else {
          productSelect.innerHTML = '<option value="">Select Product</option>'; // Reset if no branch selected
        }
      });
    });
  </script>
</body>

</html>