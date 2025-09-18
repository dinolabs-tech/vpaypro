<?php
include('models/admin_edit_category.php');
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
              <h3 class="fw-bold mb-3">Edit Category</h3>
              <nav>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                  <li class="breadcrumb-item"><a href="admin_categories.php">Categories</a></li>
                  <li class="breadcrumb-item active">Edit Category</li>
                </ol>
              </nav>
            </div>

            <section class="section">
      <div class="row">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Edit Category</h5>

              <?php if ($message): ?>
                <div class="alert alert-info">
                  <?= htmlspecialchars($message) ?>
                </div>
              <?php endif; ?>

              <form method="POST">
                <input type="hidden" name="action" value="edit_category">
                <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                <div class="row mb-3">
                  <label for="name" class="col-sm-3 col-form-label">Name</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="description" class="col-sm-3 col-form-label">Description</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($category['description']) ?></textarea>
                  </div>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-primary rounded"><i class="fas fa-save"></i></button>
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
  </body>
</html>
