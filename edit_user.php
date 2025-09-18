<?php
session_start();
require_once 'db_connect.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role_id = (int)$_POST['role_id'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username = ?, email = ?, password = ?, role_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $username, $email, $hashed_password, $role_id, $user_id);
    } else {
        $sql = "UPDATE users SET username = ?, email = ?, role_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $username, $email, $role_id, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "User updated successfully.";
    } else {
        $_SESSION['error_message'] = "Error updating user: " . $stmt->error;
    }

    $stmt->close();
    header('Location: users.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php') ?>

<body class="js">



    <!-- Header -->
    <?php include('components/header.php'); ?>
    <!--/ End Header -->

    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="index.php">Home<i class="ti-arrow-right"></i></a></li>
                            <li><a href="users.php">Users<i class="ti-arrow-right"></i></a></li>
                            <li class="active">Edit User</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <div class="container p-5">
        <div class="form-main">
            <form action="edit_user.php?id=<?php echo $user_id; ?>" method="post">
                <div class="row g-2">
                    <div class="col-md-3">
                        <div class="form-group">
                            <i><small>Username</small></i>
                            <input class="form-control px-3" type="text" name="username" id="username" value="<?php echo $user['username']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <i><small>Email</small></i>
                            <input class="form-control px-3" type="email" name="email" id="email" value="<?php echo $user['email']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <i><small>Leave blank to keep current password </small></i>
                            <input class="form-control px-3" placeholder="Password" type="password" name="password" id="password">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <i><small>Role</small></i>
                            <select class="form-control form-select" name="role_id" id="role_id" required>
                                <?php
                                $roles = [1 => 'Administrator', 2 => 'Manager'];
                                foreach ($roles as $role_id_option => $role_name) {
                                    $selected = ($user['role_id'] == $role_id_option) ? 'selected' : '';
                                    echo "<option value=\"$role_id_option\" $selected>$role_name</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <button class="btn rounded fa fa-save px-3 mx-3 text-white" style="font-size: 18px" type="submit"></button>
            </form>
        </div>
    </div>

</body>

</html>