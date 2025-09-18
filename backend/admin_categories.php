<?php
include('models/admin_categories.php');
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
            <h3 class="fw-bold mb-3">Manage Categories</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Categories</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">
              <div class="col-lg-6">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Add New Category</h5>
                    <?php if ($message): ?>
                      <div class="alert alert-info">
                        <?= htmlspecialchars($message) ?>
                      </div>
                    <?php endif; ?>

                    <form method="POST">
                      <input type="hidden" name="action" value="add_category">
                      <div class="row mb-3">
                        <label for="name" class="col-sm-3 col-form-label">Name</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                      </div>
                      <div class="row mb-3">
                        <label for="description" class="col-sm-3 col-form-label">Description</label>
                        <div class="col-sm-9">
                          <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                      </div>
                      <div class="text-center">
                        <button type="submit" class="btn btn-primary rounded"><i class="fas fa-plus"></i></button>
                      </div>
                    </form>

                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">All Categories</h5>

                    <!-- Table with stripped rows -->
                    <table class="table datatable" id="basic-datatables">
                      <thead>
                        <tr>
                          <th scope="col">S/N</th>
                          <th scope="col">Name</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($categories as $category): ?>
                          <tr>
                            <th scope="row"><?= $category['id'] ?></th>
                            <td><?= htmlspecialchars($category['name']) ?></td>
                            <td>
                              <a href="admin_edit_category.php?id=<?= $category['id'] ?>" class="btn btn-info btn-sm rounded"><i class="fas fa-edit mb-2"></i></a>
                              <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="delete_category">
                                <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm rounded" onclick="return confirm('Are you sure you want to delete this category?')"><i class="fas fa-trash mb-2"></i></button>
                              </form>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                    <!-- End Table with stripped rows -->

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