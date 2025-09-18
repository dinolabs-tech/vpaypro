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

// Fetch supplier data
$stmt = $conn->prepare('SELECT name, phone, email, address, profile_picture FROM suppliers WHERE id = ?');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($name, $phone, $email, $address, $profile_picture);
$stmt->fetch();
$stmt->close();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $address = $_POST['address'];
  $phone = $_POST['phone'];


  // Update name, email, address
  $stmt = $conn->prepare('UPDATE suppliers SET name = ?, email = ?, address = ?, phone = ?, profile_picture = ? WHERE id = ?');
  $stmt->bind_param('sssssi', $name, $email, $address, $phone, $profile_picture, $_SESSION['user_id']);
  $stmt->execute();
  $stmt->close();

  // Update password if provided
  if (!empty($_POST['password'])) {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare('UPDATE suppliers SET password = ? WHERE id = ?');
    $stmt->bind_param('si', $password, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
  }

  // Handle profile picture upload
  if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $target_dir = "assets/img/";
    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0777, true);
    }

    // Get current profile picture from DB
    $stmt = $conn->prepare('SELECT profile_picture FROM suppliers WHERE id = ?');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($current_image);
    $stmt->fetch();
    $stmt->close();

    // Delete old image if not default
    if (!empty($current_image) && $current_image !== 'default.jpg') {
      $old_file = $target_dir . $current_image;
      if (file_exists($old_file)) {
        unlink($old_file);
      }
    }

    // Upload new file
    $profile_picture = time() . "_" . basename($_FILES["profile_picture"]["name"]);
    $target_file = $target_dir . $profile_picture;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is valid
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check !== false) {
      if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
          $_SESSION['profile_picture'] = $profile_picture;

          // Update profile picture in database
          $stmt = $conn->prepare('UPDATE suppliers SET profile_picture = ? WHERE id = ?');
          $stmt->bind_param('si', $profile_picture, $_SESSION['user_id']);
          $stmt->execute();
          $stmt->close();
        }
      }
    }
  }

  // Refresh page to show new details
  header('Location: supplier_profile.php');
  exit;
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
              <h3 class="fw-bold mb-3">Profile</h3>
              <nav>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                  <li class="breadcrumb-item active">Profile</li>
                </ol>
              </nav>
            </div>

            <section class="section profile">
      <div class="row">
        <div class="col-xl-4">

          <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

              <img src="assets/img/<?php echo $profile_picture; ?>" alt="Profile Image" class="rounded-circle" style="width: 120px; height: 120px;">
              <h2><?php echo $name; ?></h2>
            </div>
          </div>

        </div>

        <div class="col-xl-8">

          <div class="card">
            <div class="card-body pt-3">
              <h5 class="card-title">Edit Profile</h5>

              <!-- Profile Edit Form -->
              <form method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                  <label for="profile_picture" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                  <div class="col-md-8 col-lg-9">
                    <input class="form-control" type="file" id="profile_picture" name="profile_picture">
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="name" class="col-md-4 col-lg-3 col-form-label">Name</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="name" type="text" class="form-control" id="name" value="<?php echo $name; ?>">
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="email" class="col-md-4 col-lg-3 col-form-label">Mobile</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="phone" type="number" class="form-control" id="phone" value="<?php echo $phone; ?>">
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="email" type="email" class="form-control" id="email" value="<?php echo $email; ?>">
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="address" class="col-md-4 col-lg-3 col-form-label">Address</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="address" type="text" class="form-control" id="address" value="<?php echo $address; ?>">
                  </div>
                </div>
                <!-- 
              <div class="row mb-3">
                <label for="country" class="col-md-4 col-lg-3 col-form-label">Country</label>
                <div class="col-md-8 col-lg-9">
                  <input name="country" type="text" class="form-control" id="country" value="<?php echo $country; ?>">
                </div>
              </div>

              <div class="row mb-3">
                <label for="state" class="col-md-4 col-lg-3 col-form-label">State</label>
                <div class="col-md-8 col-lg-9">
                  <input name="state" type="text" class="form-control" id="state" value="<?php echo $state; ?>">
                </div>
              </div> -->

                <div class="row mb-3">
                  <label for="password" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="password" type="password" class="form-control" id="password">
                  </div>
                </div>

                <div class="text-center">
                  <button type="submit" name="update_profile" class="btn btn-primary rounded">Save Changes</button>
                </div>
              </form><!-- End Profile Edit Form -->

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
