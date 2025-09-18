<?php include('models/suppliers.php'); ?>

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
            <h3 class="fw-bold mb-3">Suppliers</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Suppliers</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <h4 class="card-title">Suppliers&nbsp;|&nbsp;<span><small>New Supplier</small></span></h4>
                    </div>
                  </div>
                  <div class="card-body pb-0">
                    <div class="mb-4 mt-2">
                      <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                        <div class="alert alert-success">Supplier deleted successfully.</div>
                      <?php endif; ?>

                      <form method="post" class="row g-3">
                        <?php if (isset($editMode) && $editMode): ?>
                          <input type="hidden" name="edit_id" value="<?= htmlspecialchars($editId) ?>">
                        <?php endif; ?>

                        <div class="col-md-4">
                          <input class="form-control" type="text" name="name" id="name" placeholder="Supplier's Name"
                            required value="<?= isset($productToEdit) ? htmlspecialchars($productToEdit['name']) : '' ?>" required>
                        </div>

                        <div class="col-md-4">
                          <input class="form-control" type="text" name="product" id="product"
                            placeholder="Product Name" required
                            value="<?= isset($productToEdit) ? htmlspecialchars($productToEdit['product']) : '' ?>" required>
                        </div>

                        <div class="col-md-4">
                          <input class="form-control" type="text" name="companyname" id="companyname" placeholder="Business Name"
                            required value="<?= isset($productToEdit) ? htmlspecialchars($productToEdit['companyname']) : '' ?>" required>
                        </div>

                        <div class="col-md-2">
                          <input class="form-control" type="text" name="phone" id="phone"
                            placeholder="Mobile" required
                            value="<?= isset($productToEdit) ? htmlspecialchars($productToEdit['phone']) : '' ?>" required>
                        </div>

                        <div class="col-md-6">
                          <input class="form-control" type="text" name="address" id="address" placeholder="Address" required
                            value="<?= isset($productToEdit) ? htmlspecialchars($productToEdit['address']) : '' ?>">
                        </div>

                        <div class="col-md-2">
                          <input class="form-control" type="text" name="email" id="email"
                            placeholder="Business Email"
                            value="<?= isset($productToEdit) ? htmlspecialchars($productToEdit['email']) : '' ?>" required>
                        </div>

                        <div class="col-md-2">
                          <input class="form-control" type="password" name="password" id="password" placeholder="Password" required
                            value="<?= isset($productToEdit) ? htmlspecialchars($productToEdit['password']) : '' ?>">
                        </div>

                        <div class="col-md-4">
                          <button type="submit" class="btn btn-success rounded"><span class="btn-label"><i class="fas fa-save"></i></span></button>
                          <button type="button" class="btn btn-dark rounded" id="resetForm"><span class="btn-label"><i class="fa fa-sync"></i></span></button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Product Table -->
            <div class="row mt-4">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <span class="card-title">Suppliers List</span>
                    </div>
                    <br>
                    <div class="table-responsive">
                      <table class="table table-bordered datatable" id="basic-datatables">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Product</th>
                            <th>Business Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($suppliers as $supplier): ?>
                            <tr>
                              <td><?= htmlspecialchars($supplier['id']) ?></td>
                              <td><?= htmlspecialchars($supplier['name']) ?></td>
                              <td><?= htmlspecialchars($supplier['product']) ?></td>
                              <td><?= htmlspecialchars($supplier['companyname']) ?></td>
                              <td><?= htmlspecialchars($supplier['phone']) ?></td>
                              <td><?= htmlspecialchars($supplier['email']) ?></td>
                              <td><?= htmlspecialchars($supplier['address']) ?></td>
                              <td>
                                <a href="?id=<?= urlencode($supplier['id']) ?>" class="btn btn-sm btn-primary rounded mb-2"><i
                                    class="fas fa-edit"></i></a>
                                <a href="delete_supplier.php?id=<?= urlencode($supplier['id']) ?>"
                                  class="btn btn-sm btn-danger rounded mb-2"
                                  onclick="return confirm('Are you sure you want to delete this Supplier?');"><i
                                    class="fas fa-trash"></i></a>
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
  <script>
    document.getElementById("resetForm").addEventListener("click", function() {
      document.getElementById("name").value = "";
      document.getElementById("product").value = "";
      document.getElementById("companyname").value = "";
      document.getElementById("phone").value = "";
      document.getElementById("email").value = "";
      document.getElementById("address").value = "";
    });
  </script>
</body>

</html>