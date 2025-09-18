<?php
session_start();
require_once 'db_connect.php';


if (!isset($_SESSION['username'])) {
	header("Location: login.php");
	exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = $_POST['username'];
	$email = $_POST['email'];
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	$role_id = $_POST['role_id'];

	$stmt = $conn->prepare("INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)");
	$stmt->bind_param("sssi", $username, $email, $password, $role_id);
	$stmt->execute();
	$stmt->close();

	header('Location: users.php');
	exit;
}
?>


<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php') ?>

<body class="js">

	<!-- Eshop Color Plate -->
	<div class="color-plate ">
		<a class="color-plate-icon"><i class="ti-paint-bucket"></i></a>
		<h4>Eshop Colors</h4>
		<p>Here is some awesome color's available on Eshop Template.</p>
		<span class="color1"></span>
		<span class="color2"></span>
		<span class="color3"></span>
		<span class="color4"></span>
		<span class="color5"></span>
		<span class="color6"></span>
		<span class="color7"></span>
		<span class="color8"></span>
		<span class="color9"></span>
		<span class="color10"></span>
		<span class="color11"></span>
		<span class="color12"></span>
	</div>
	<!-- /End Color Plate -->

	<!-- Header -->
	<?php include('components/header.php') ?>
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
							<li class="active">Add new user</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->

	<!-- Product Style 1 -->
	<div class="container p-5">
		<div class="form-main">
			<form action="add_user.php" method="post">
				<div class="row g-2">
					<div class="col-md-3">
						<div class="form-group">
							<input class="form-control px-3" type="text" name="username" id="username" placeholder="Username" required>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input class="form-control px-3" type="email" name="email" id="email" placeholder="Email" required>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input class="form-control px-3" type="password" name="password" id="password" placeholder="Password" required>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
								<select class="form-control form-select" name="role_id" id="role_id" required>
									<option value="" selected disabled>Select Role</option>
									<option value="1">Administrator</option>
									<option value="2">Manager</option>
								</select>
						</div>
					</div>
				</div>
				<button class="btn rounded fa fa-save px-3 text-white" style="font-size: 18px" type="submit"></button>
		</div>
		</form>

	</div>

	<!--/ End Product Style 1  -->
	<!-- Start Footer Area -->
	<?php include('components/footer.php') ?>
	<!-- /End Footer Area -->


	<!-- Jquery -->
	<?php include('components/script.php') ?>
</body>

</html>