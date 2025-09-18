<?php
session_start();
require_once 'config.php';
require_once 'includes/functions.php';
require_once 'db_connect.php'; // make sure this sets $conn (MySQLi connection)


$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = trim($_POST['username']);
	$email = trim($_POST['email']);
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	$first_name = trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$contact = trim($_POST['contact']);
	$address = trim($_POST['address']);
	$country = trim($_POST['country']);
	$state = trim($_POST['state']);

	// Check if username or email already exists
	$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR email = ?");
	mysqli_stmt_bind_param($stmt, "ss", $username, $email);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_store_result($stmt);

	if (mysqli_stmt_num_rows($stmt) > 0) {
		$error = 'Username or email already exists.';
		mysqli_stmt_close($stmt);
	} else {
		mysqli_stmt_close($stmt);

		// Get role_id for 'customer'
		$role_id = 2;

		// Insert new user
		$stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, role_id, first_name, last_name, contact, address, country, state) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		mysqli_stmt_bind_param($stmt, "sssissssss", $username, $email, $password, $role_id, $first_name, $last_name, $contact, $address, $country, $state);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		// Get inserted user ID
		$user_id = mysqli_insert_id($conn);
		$_SESSION['user_id'] = $user_id;
		$_SESSION['username'] = $username;
		$_SESSION['first_name'] = $first_name;
		$_SESSION['last_name'] = $last_name;
		$_SESSION['role_id'] = $role_id;
		$_SESSION['country'] = $country;
		$_SESSION['state'] = $state;

		header("Location: index.php");
		exit;
	}
}
?>


<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php') ?>

<body class="js">

	<!-- Eshop Color Plate -->

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
							<li class="active">Register</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->

	<!-- Shop Login -->
	<section class="shop login section">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 offset-lg-3 col-12">
					<div class="login-form">
						<h2>Register</h2>
						<p>Please register in order to checkout more quickly</p>
						<!-- Form -->
						<?php if ($error): ?>
							<p class="error"><?php echo $error; ?></p>
						<?php endif; ?>
						<form class="form" method="post" action="register.php">
							<div class="row">
								<div class="col-12">
									<div class="form-group">
										<input type="text" name="first_name" id="first_name" required="required" placeholder="First Name">
									</div>
								</div>
								<div class="col-12">
									<div class="form-group">
										<input type="text" name="last_name" id="last_name" placeholder="Last Name" required="required">
									</div>
								</div>
								<div class="col-12">
									<div class="form-group">
										<input type="text" name="email" id="email" placeholder="Email" required="required">
									</div>
								</div>
								<div class="col-12">
									<div class="form-group">
										<input type="tel" name="contact" id="contact" placeholder="Contact" required="required">
									</div>
								</div>
								<div class="col-12">
									<div class="form-group">
										<textarea class="px-3" type="text" name="address" id="address" placeholder="Address" required="required"></textarea>
									</div>
								</div>
								<div class="col-12">
									<div class="form-group">
										<select name="country" id="country" class="form-control" required="required">
											<option value="">Select Country</option>
										</select>
									</div>
								</div>
								<div class="col-12">
									<div class="form-group">
										<select name="state" id="state" class="form-control" required="required">
											<option value="">Select State</option>
										</select>
									</div>
								</div>
								<div class="col-12">
									<div class="form-group">
										<input type="text" name="username" id="username" placeholder="Username" required="required">
									</div>
								</div>
								<div class="col-12">
									<div class="form-group">
										<input type="password" name="password" id="password" placeholder="Password" required="required">
									</div>
								</div>

								<div class="col-12">
									<div class="form-group login-btn">
										<button class="btn rounded" type="submit">Register</button>
										<a href="login.php" class="btn rounded">Login</a>
									</div>
									<div class="checkbox">
										<label class="checkbox-inline" for="2"><input name="news" id="2" type="checkbox">Sign Up for Newsletter</label>
									</div>
								</div>
							</div>
						</form>
						<!--/ End Form -->
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--/ End Login -->

	<!-- Start Footer Area -->
	<?php include('components/script.php') ?>
	<!-- /End Footer Area -->

	<!-- Jquery -->
	<?php include('components/script.php') ?>
	<script src="https://cdn.jsdelivr.net/gh/hassanzohair/country-state-city-picker-package@main/dist/country-state-city-picker.min.js"></script>
	<script>
		$(document).ready(function() {
			loadCountries();

			$('#country').on('change', function() {
				var countryCode = $(this).val();
				if (countryCode) {
					loadStates(countryCode);
				} else {
					$('#state').empty().append('<option value="">Select State</option>');
				}
			});
		});
	</script>
</body>

</html>
