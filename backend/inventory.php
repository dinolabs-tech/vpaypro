<?php
include('models/inventory.php');
include('models/branches.php'); // Include the Branch model

$branchModel = new Branch($conn); // Instantiate Branch model
$user_country = $_SESSION['country'] ?? null;
$user_state = $_SESSION['state'] ?? null;
// Get branches filtered by user's country and state if available, otherwise get all branches
$branches = $branchModel->getAllBranches($user_country, $user_state);
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
            <h3 class="fw-bold mb-3">Inventory</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Inventory</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <?php if ($_SESSION['role'] != 'Sales Manager') { ?>
              <div class="row">
                <div class="col-md-12">
                  <div class="card card-round">
                    <div class="card-header">
                      <div class="card-head-row">
                        <h4 class="card-title">Inventory&nbsp;|&nbsp;<span><small>New Inventory</small></span></h4>
                      </div>
                    </div>

                    <div class="card-body pb-0">
                      <div class="mb-4 mt-2">
                        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                          <div class="alert alert-success">Product deleted successfully.</div>
                        <?php endif; ?>

                        <form method="post" class="row g-3" enctype="multipart/form-data">
                          <?php if (isset($editMode) && $editMode): ?>
                            <input type="hidden" name="edit_id" value="<?= htmlspecialchars($editId) ?>">
                          <?php endif; ?>
                          <input type="hidden" name="country" value="<?= htmlspecialchars($user_country ?? '') ?>">
                          <input type="hidden" name="state" value="<?= htmlspecialchars($user_state ?? '') ?>">

                          <div class="col-md-6">
                            <input class="form-control" type="text" name="productname" id="productname"
                              placeholder="Product Name" required
                              value="<?= isset($productToEdit) ? htmlspecialchars($productToEdit['productname']) : '' ?>">
                          </div>

                          <div class="col-md-2">
                            <input class="form-control" type="text" name="sku" id="sku"
                              placeholder="SKU"
                              value="<?= isset($productToEdit) ? htmlspecialchars($productToEdit['sku']) : '' ?>">
                          </div>

                          <div class="col-md-2">
                            <input class="form-control" type="number" step="0.01" name="unitprice" id="unitprice"
                              placeholder="Unit Price" required
                              value="<?= isset($productToEdit) ? htmlspecialchars($productToEdit['unitprice']) : '' ?>">
                          </div>

                          <div class="col-md-2">
                            <input class="form-control" type="number" step="0.01" name="sellprice" id="sellprice"
                              placeholder="Sell Price" required
                              value="<?= isset($productToEdit) ? htmlspecialchars($productToEdit['sellprice']) : '' ?>">
                          </div>

                          <div class="col-md-3">
                            <select id="branch_id" name="branch_id" class="form-select" required>
                              <option value="" selected disabled>Select Branch</option>
                              <?php foreach ($branches as $branch): ?>
                                <option value="<?= $branch['branch_id'] ?>"
                                  <?= (isset($productToEdit['branch_id']) && $productToEdit['branch_id'] == $branch['branch_id']) ? 'selected' : '' ?>>
                                  <?= htmlspecialchars($branch['branch_name']) ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>

                          <div class="col-md-2">
                            <input class="form-control" type="number" name="initial_quantity" id="initial_quantity"
                              placeholder="Quantity" required min="0"
                              value="<?= isset($productToEdit) ? htmlspecialchars($productToEdit['initial_quantity']) : '' ?>">
                          </div>

                          <div class="col-md-7">
                            <input class="form-control" type="text" name="description" id="description"
                              placeholder="Description"
                              value="<?= isset($productToEdit) ? htmlspecialchars($productToEdit['description']) : '' ?>">

                          </div>

                          <div class="col-md-6">
                            <input class="form-control" type="file" name="product_image" id="product_image" accept="image/*">
                          </div>
                          <?php if ($editMode && !empty($productToEdit['image_url']) && $productToEdit['image_url'] !== 'assets/img/products/default.jpg'): ?>
                            <div class="col-md-6">
                              <p>Current Image: <?= htmlspecialchars(basename($productToEdit['image_url'])) ?></p>
                            </div>
                          <?php endif; ?>

                          <div class="col-md-2">
                            <input class="form-control" type="number" name="reorder_level" id="reorder_level"
                              placeholder="Reorder Level"
                              value="<?= isset($productToEdit) ? htmlspecialchars($productToEdit['reorder_level']) : '' ?>" required>
                          </div>
                          <div class="col-md-2">
                            <input class="form-control" type="number" name="reorder_qty" id="reorder_qty"
                              placeholder="Reorder Quantity"
                              value="<?= isset($productToEdit) ? htmlspecialchars($productToEdit['reorder_qty']) : '' ?>" required>
                          </div>

                          <div class="col-md-6">
                            <label for="categories">Product Categories</label>
                            <select id="categories" name="categories[]" class="form-select" multiple>
                              <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"
                                  <?php if (isset($productToEdit['categories']) && in_array($category['id'], $productToEdit['categories'])) echo 'selected'; ?>>
                                  <?= htmlspecialchars($category['name']) ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>

                          <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary rounded"><?= $editMode ? '<i class="fas fa-save"></i>' : '<i class="fas fa-plus"></i>' ?></button>
                            <?php if ($editMode): ?>
                              <a href="inventory.php" class="btn btn-secondary rounded">Cancel Edit</a>
                            <?php endif; ?>
                          </div>
                        </form>
                      </div>

                    </div>

                  </div>
                </div>
              </div>
            <?php } ?>

            <!-- Product Table -->
            <div class="row mt-4">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <span class="card-title">Product List</span>
                    </div>
                    <!-- <div>
                <button onclick="exportToPdf()" class="btn btn-primary mt-3"><i class="bi bi-file-pdf"></i> Export to PDF</button>
              </div> -->
                    <br>
                    <div class="table-responsive">
                      <table class="table table-bordered datatable" id="basic-datatables">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>SKU</th> <!-- Add SKU column header -->
                            <th>Cost Price</th>
                            <th>Selling Price</th>
                            <th>Quantity (Total)</th>
                            <th>Image</th>
                            <th>Description</th>
                            <?php if ($_SESSION['role'] != 'Sales Manager') { ?>
                              <th>Actions</th>
                            <?php } ?>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($products as $product): ?>
                            <tr>
                              <td><?= htmlspecialchars($product['productid']) ?></td>
                              <td><?= htmlspecialchars($product['productname']) ?></td>
                              <td><?= htmlspecialchars($product['sku']) ?></td>
                              <td><?= number_format($product['unitprice']) ?></td>
                              <td><?= number_format($product['sellprice']) ?></td>
                              <td><?= htmlspecialchars($product['total_quantity']) ?></td>
                              <td>
                                <?php
                                $imageUrl = !empty($product['image_url']) ? $product['image_url'] : 'assets/img/default.jpg'; // Use default image if not set
                                ?>
                                <img src="<?= htmlspecialchars($imageUrl) ?>" class="rounded" alt="<?= htmlspecialchars($product['productname']) ?>" width="50" height="50" style="object-fit: cover;">
                              </td>
                              <td><?= htmlspecialchars($product['description']) ?></td>
                              <?php if ($_SESSION['role'] != 'Sales Manager') { ?>
                                <td>
                                  <a href="?id=<?= urlencode($product['productid']) ?>" class="btn btn-sm btn-primary rounded"><i
                                      class="fas fa-edit"></i></a>
                                  <!-- <a href="manage_product_variations.php?product_id=<?= urlencode($product['productid']) ?>" class="btn btn-sm btn-info">Variations</a> -->
                                  <!-- <a href="delete_product.php?id=<?= urlencode($product['productid']) ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure you want to delete this product?');"><i
                              class="ri ri-delete-bin-2-line"></i></a> -->
                                </td>
                              <?php } ?>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                      <script>
                        function exportToPdf() {
                          const jsPDF = window.jspdf.jsPDF;
                          const doc = new jsPDF();

                          // Add table headers
                          const headers = ["ID", "Product Name", "SKU", "Cost Price", "Selling Price", "Quantity (Total)", "Description"];

                          // Add table rows
                          const rows = [];
                          const table = document.querySelector(".table");
                          const tableBody = table.querySelector("tbody");
                          const tableRows = tableBody.querySelectorAll("tr");

                          tableRows.forEach(row => {
                            const rowData = [];
                            const cells = row.querySelectorAll("td");
                            cells.forEach((cell, index) => {
                              if (index < 6) { // Exclude the "Actions" column
                                rowData.push(cell.textContent);
                              }
                            });
                            rows.push(rowData);
                          });

                          doc.autoTable({
                            head: [headers],
                            body: rows,
                          });

                          // Save the PDF
                          doc.save('inventory.pdf');
                        }
                      </script>
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
  <script>
    document.getElementById("resetForm").addEventListener("click", function() {
      document.getElementById("productname").value = "";
      document.getElementById("sellprice").value = "";
      document.getElementById("unitprice").value = "";
      document.getElementById("initial_quantity").value = "";
      document.getElementById("description").value = "";
      document.getElementById("reorder_level").value = "";
      document.getElementById("reorder_qty").value = "";
      document.getElementById("branch_id").selectedIndex = 0; // Reset to first option
    });

    document.addEventListener('DOMContentLoaded', function() {
      console.log('DOMContentLoaded fired in inventory.php');
      try {
        const addVariationBtn = document.getElementById('add-variation');
        const variationsContainer = document.getElementById('variations-container');

        if (addVariationBtn) {
          addVariationBtn.addEventListener('click', function() {
            console.log('Add Variation button clicked');
            addVariationRow();
          });
        } else {
          console.error('Add Variation button not found!');
        }

        if (variationsContainer) {
          variationsContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-variation')) {
              console.log('Remove Variation button clicked');
              event.target.closest('.variation-item').remove();
            }
          });
        } else {
          console.error('Variations container not found!');
        }

      } catch (error) {
        console.error('Error in DOMContentLoaded for inventory.php:', error);
      }
    });
  </script>
</body>

</html>