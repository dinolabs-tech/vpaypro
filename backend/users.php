<?php include('models/users.php'); ?>

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
            <h3 class="fw-bold mb-3">User Control</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">User Control</li>
              </ol>
            </nav>
          </div>
          <section class="section">
            <div class="row">
              <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">User Account&nbsp;|&nbsp;<span><small>New User</small></span></div>
                    </div>
                  </div>
                  <div class="card-body pb-0">
                    <div class="mb-4 mt-2">
                      <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                        <div class="alert alert-success">User Account deleted successfully.</div>
                      <?php endif; ?>

                      <form method="post" class="row g-3">
                        <?php if (isset($editMode) && $editMode): ?>
                          <input type="hidden" name="edit_id" value="<?= htmlspecialchars($editId) ?>">
                        <?php endif; ?>

                        <div class="col-md-6">
                          <input class="form-control" type="text" name="name" id="name" placeholder="Name" required
                            value="<?= isset($staffToEdit) ? htmlspecialchars($staffToEdit['staffname']) : '' ?>">
                        </div>

                        <div class="col-md-6">
                          <input class="form-control" type="text" name="username" id="username" placeholder="Username"
                            required value="<?= isset($staffToEdit) ? htmlspecialchars($staffToEdit['username']) : '' ?>">
                        </div>

                        <?php if (!isset($editMode) || !$editMode): ?>
                          <div class="col-md-6">
                            <input class="form-control" type="password" name="password" id="password" placeholder="Password"
                              required>
                          </div>
                        <?php endif; ?>

                        <div class="col-md-6">
                          <select class="form-control form-select" name="role" id="role">

                            <?php
                            $currentUserRole = $_SESSION['role']; // The logged-in user role
                            if ($currentUserRole == 'Superuser') { ?>
                              <option value="CEO" <?= isset($staffToEdit) && $staffToEdit['role'] == 'CEO' ? 'selected' : '' ?>>CEO</option>
                            <?php } ?>

                            <option value="Administrator" <?= isset($staffToEdit) && $staffToEdit['role'] == 'Administrator' ? 'selected' : '' ?>>Administrator</option>
                            <option value="Sales Manager" <?= isset($staffToEdit) && $staffToEdit['role'] == 'Sales Manager' ? 'selected' : '' ?>>Sales Manager</option>
                            <option value="Inventory Manager" <?= isset($staffToEdit) && $staffToEdit['role'] == 'Inventory Manager' ? 'selected' : '' ?>>Inventory Manager</option>
                            <option value="Cashier" <?= isset($staffToEdit) && $staffToEdit['role'] == 'Cashier' ? 'selected' : '' ?>>Sales Associate</option>
                            <option value="Delivery" <?= isset($staffToEdit) && $staffToEdit['role'] == 'Delivery' ? 'selected' : '' ?>>Delivery Personnel</option>

                          </select>


                        </div>

                        <!-- Branch Selection -->
                        <div class="col-md-6">
                          <select class="form-control form-select" name="branch_id" id="branch_id" required>
                            <option value="" selected disabled>Select Branch</option>
                            <?php foreach ($branches as $branch): ?>
                              <option value="<?= htmlspecialchars($branch['branch_id']) ?>" <?= (isset($staffToEdit) && $staffToEdit['branch_id'] == $branch['branch_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($branch['branch_name']) ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>

                        <!-- Country and State Selection -->
                        <div class="col-md-6">
                          <select class="form-control form-select" name="country" id="country" required>
                            <option value="" selected disabled>Select Country</option>
                            <?php foreach ($countries as $country): ?>
                              <option value="<?= htmlspecialchars($country['name']) ?>" <?= (isset($staffToEdit) && $staffToEdit['country'] == $country['name']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($country['name']) ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>

                        <div class="col-md-6">
                          <select class="form-control form-select" name="state" id="state" required>
                            <option value="" selected disabled>Select State/Province</option>
                            <?php foreach ($states as $state): ?>
                              <option value="<?= htmlspecialchars($state['name']) ?>" <?= (isset($staffToEdit) && $staffToEdit['state'] == $state['name']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($state['name']) ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <!-- End Country and State Selection -->


                        <div class="col-md-4">
                          <button type="submit" class="btn btn-success rounded"><span class="btn-label"><i
                                class="fa fa-save"></i></span></button>
                          <a href="users.php">
                            <button type="button" class="btn btn-dark rounded"><span class="btn-label"><i
                                  class="fa fa-sync"></i></span></button></a>

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
                      <span class="card-title">Users List</span>
                    </div>
                    <br>
                    <div class="table-responsive">
                      <table class="table table-bordered datatable" id="basic-datatables">
                        <thead>
                          <tr>
                            <th style="display: none;">ID</th>
                            <th>Staff Name</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Branch</th> <!-- Added Branch column -->
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          // Create a map for branch IDs to branch names
                          $branchMap = [];
                          foreach ($branches as $branch) {
                            $branchMap[$branch['branch_id']] = $branch['branch_name'];
                          }
                          ?>
                          <?php foreach ($suppliers as $supplier): ?>
                            <tr>
                              <td style="display: none;"><?= htmlspecialchars($supplier['staffid']) ?></td>
                              <td><?= htmlspecialchars($supplier['staffname']) ?></td>
                              <td><?= htmlspecialchars($supplier['username']) ?></td>
                              <td><?= htmlspecialchars($supplier['role']) ?></td>
                              <td><?= htmlspecialchars($branchMap[$supplier['branch_id']] ?? 'N/A') ?></td> <!-- Display Branch Name -->
                              <td>
                                <a href="?id=<?= urlencode($supplier['id']) ?>" class="btn btn-sm btn-primary rounded"><i
                                    class="fas fa-edit"></i></a>
                                <a href="delete_user.php?id=<?= urlencode($supplier['id']) ?>" class="rounded btn btn-sm btn-danger"
                                  onclick="return confirm('Are you sure you want to delete this user?');"><i
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
    // Basic script for form reset if needed
    document.addEventListener('DOMContentLoaded', function() {
      // You can add any other necessary JS here, but the dynamic population is removed.
    });
  </script>
</body>

</html>