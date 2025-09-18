<?php
session_start();
require_once 'db_connect.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['staffname'])) {
    header("Location: login.php");
    exit;
}

// Handle user actions (add, edit, delete)
// ...

$sql = "SELECT * FROM users where role_id != 0";
$users = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php')?>
<body class="js">
	
	<!-- Eshop Color Plate -->
	
	<!-- /End Color Plate -->
		
		<!-- Header -->
	<?php include('components/header.php')?>
		<!--/ End Header -->
		
		<!-- Breadcrumbs -->
		<div class="breadcrumbs">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<div class="bread-inner">
							<ul class="bread-list">
								<li><a href="index1.html">Home<i class="ti-arrow-right"></i></a></li>
								<li class="active">Users</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Breadcrumbs -->
		
		<!-- Product Style -->
		<div class="container table-responsive">
            <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="success-message">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>
        <a href="add_user.php" class="btn rounded text-white fa fa-plus mt-3 mb-3 px-3 py-2" style="font-size: 28px"></a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td>
                            <?php
                            $roles = [1 => 'Administrator', 2 => 'Manager'];
                            echo isset($roles[$user['role_id']]) ? $roles[$user['role_id']] : 'Unknown';
                            ?>
                        </td>
                        <td>
                            <a class="btn rounded text-white fa fa-edit mb-3 px-3" style="font-size: 18px" href="edit_user.php?id=<?php echo $user['id']; ?>"></a>
                            <a class="btn rounded text-white fa fa-trash mb-3 px-3" style="font-size: 18px" href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?')"></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
		<!--/ End Product Style 1  -->	
		<!-- Start Footer Area -->
	<?php include('components/footer.php')?>
		<!-- /End Footer Area -->
	
	
    <!-- Jquery -->
<?php include('components/script.php')?>
</body>
</html>