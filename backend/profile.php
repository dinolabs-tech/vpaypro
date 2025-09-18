<?php
// profile.php 

include 'database/db_connection.php';
session_start();


// Messages
$message = '';
$pmessage = '';

// Handle password change
if (isset($_POST['update_password'])) {
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];

  if ($new_password !== $confirm_password) {
    $message = "New passwords do not match.";
  } else {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("UPDATE login SET password=? WHERE id=?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    if ($stmt->execute()) {
      $message = "Password changed successfully!";
    } else {
      $message = "Error changing password: " . $stmt->error;
    }
    $stmt->close();
  }
}


// Handle profile update (including image)
if (isset($_POST['update_profile'])) {
  $user_id = $_SESSION['user_id'];
  $staffname = $_POST['staffname'];
  $email = $_POST['email'];
  $mobile = $_POST['mobile'];
  $address = $_POST['address'];

  // Get current image from DB
  $stmt = $conn->prepare("SELECT profile_picture FROM login WHERE id=?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $stmt->bind_result($current_image);
  $stmt->fetch();
  $stmt->close();

  $profile_picture_filename = null;

  // Handle profile picture upload
  if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $target_dir = "assets/img/profile_pictures/";
    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0777, true);
    }

    // Delete old image if exists and is not the default
    if (!empty($current_image) && $current_image !== 'profile-img.jpg') {
      $old_file = $target_dir . $current_image;
      if (file_exists($old_file)) {
        unlink($old_file);
      }
    }

    // Upload new file
    $profile_picture_filename = time() . "_" . basename($_FILES["profile_picture"]["name"]);
    $target_file = $target_dir . $profile_picture_filename;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check !== false) {
      if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        if (!move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
          $pmessage = "Error uploading file.";
          $profile_picture_filename = null;
        }
        $_SESSION['profile_picture'] = $profile_picture_filename;
      } else {
        $pmessage = "Invalid file format. Only JPG, JPEG, PNG & GIF allowed.";
        $profile_picture_filename = null;
      }
    } else {
      $pmessage = "File is not a valid image.";
      $profile_picture_filename = null;
    }
  }

  // Build SQL dynamically based on whether picture was uploaded
  if ($profile_picture_filename) {
    $stmt = $conn->prepare("UPDATE login SET staffname=?, email=?, mobile=?, address=?, profile_picture=? WHERE id=?");
    $stmt->bind_param("sssssi", $staffname, $email, $mobile, $address, $profile_picture_filename, $user_id);
    $_SESSION['profile_picture'] = $profile_picture_filename;
  } else {
    $stmt = $conn->prepare("UPDATE login SET staffname=?, email=?, mobile=?, address=? WHERE id=?");
    $stmt->bind_param("ssssi", $staffname, $email, $mobile, $address, $user_id);
  }

  $_SESSION['staffname'] = $staffname;
  $_SESSION['email'] = $email;
  $_SESSION['mobile'] = $mobile;
  $_SESSION['address'] = $address;

  if ($stmt->execute()) {
    $pmessage = "Profile updated successfully!";
  } else {
    $pmessage = "Error updating profile: " . $stmt->error;
  }
  $stmt->close();
}


// Fetch user details
$userid = $_SESSION['user_id'];
$sql = "SELECT *, profile_picture FROM login WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();
$user_details = $result->fetch_assoc();
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
            <h3 class="fw-bold mb-3">Profile</h3>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Profile</li>
              </ol>
            </nav>
          </div>

          <section class="section">
            <div class="row">

              <!-- Change Password -->
              <div class="col-md-6 ">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Change Password</div>
                    </div>
                  </div>
                  <div class="card-body pb-0">
                    <?php if ($message !== ''): ?>
                      <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                      <input type="password" name="new_password" placeholder="New Password" class="form-control mt-3" required>
                      <input type="password" name="confirm_password" placeholder="Confirm Password" class="form-control mt-3" required>
                      <button class="btn btn-primary mt-3 mb-3 rounded" name="update_password" type="submit">
                        <i class="fa fa-save"></i> Change Password
                      </button>
                    </form>
                  </div>
                </div>
              </div>

              <!-- Update Profile -->
              <div class="col-md-6">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Update Profile</div>
                    </div>
                  </div>
                  <div class="card-body pb-0">
                    <?php if ($pmessage !== ''): ?>
                      <div class="alert alert-info"><?php echo htmlspecialchars($pmessage); ?></div>
                    <?php endif; ?>

                    <form action="" method="post" enctype="multipart/form-data">
                      <!-- Image Display -->
                      <div class="row mb-3">
                        <label class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                        <div class="col-md-8 col-lg-9">
                          <img src="assets/img/profile_pictures/<?= !empty($user_details['profile_picture']) ? htmlspecialchars($user_details['profile_picture']) : 'profile-img.jpg'; ?>" alt="Profile" class="rounded-circle" style="height:120px; width:120px;">
                          <input class="form-control mt-2" type="file" name="profile_picture">
                        </div>
                      </div>

                      <!-- Personal Info -->
                      <input type="text" class="form-control mt-3" name="staffname" placeholder="Enter Name" value="<?php echo htmlspecialchars($user_details['staffname']); ?>">
                      <input type="email" class="form-control mt-3" name="email" placeholder="Enter Email" value="<?php echo htmlspecialchars($user_details['email']); ?>">
                      <input type="text" class="form-control mt-3" name="mobile" placeholder="Enter Mobile" value="<?php echo htmlspecialchars($user_details['mobile']); ?>">
                      <textarea class="form-control mt-3" name="address" placeholder="Enter Address" rows="5"><?php echo htmlspecialchars($user_details['address']); ?></textarea>

                      <button class="btn btn-success mt-3 mb-3 rounded" type="submit" name="update_profile">
                        <i class="fa fa-save"></i> Update Profile
                      </button>
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