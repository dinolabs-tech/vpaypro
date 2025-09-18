<?php
session_start();
require_once 'db_connect.php';

// Initialize an error message variable
$error_message = '';

// Check if Superuser exists, if not create one
$check_superuser = $conn->prepare("SELECT id FROM users WHERE role_id = '0' LIMIT 1");
$check_superuser->execute();
$check_superuser->store_result();


if ($check_superuser->num_rows == 0) {
    // Superuser doesn't exist, create one
    $stmt_superuser = $conn->prepare("INSERT INTO users (first_name, last_name, email, username, password, role_id) VALUES (?, ?, ?, ?, ?, ?)");
    $first_name = "Dinolabs";
    $last_name = "Superuser";
    $email = "enquiries@dinolabstech.com";
    $username = "dinolabs";
    $password = password_hash("dinolabs", PASSWORD_DEFAULT);
    // $password = "dinolabs"; // Note: In production, you should hash this password
    $role_id = 0;
    $stmt_superuser->bind_param("ssssss", $first_name, $last_name, $email, $username, $password, $role_id);
    $stmt_superuser->execute();
    $stmt_superuser->close();
}
$check_superuser->close();

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        // Verify the submitted password against the hashed password from the database
        if (password_verify($password, $user['password'])) {
            // if ($user['password']) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
           $_SESSION['role_id'] = $user['role_id'];

            // Redirect to the home page
            header("Location: index.php");
            exit;
        } else {
            // Set error message for incorrect password
            $error_message = "Incorrect password";
        }
    } else {
        // Set error message for incorrect username
        $error_message = "Incorrect username";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php') ?>

<body class="js">


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
							<li class="active">Login</li>
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
						<h2>Login</h2>
						<p>Please register in order to checkout more quickly</p>
						<?php if (!empty($error_message)): ?>
							<div class="error-message" style="color: red; margin-bottom: 15px;">
								<?php echo htmlspecialchars($error_message); ?>
							</div>
						<?php endif; ?>
						<!-- Form -->
						<form class="form" method="post" action="login.php">
							<div class="row">
								<div class="col-12">
									<div class="form-group">
										<label>Username<span>*</span></label>
										<input type="text" name="username" placeholder="Enter Usename" required="required">
									</div>
								</div>
								<div class="col-12">
									<div class="form-group">
										<label>Your Password<span>*</span></label>
										<input type="password" name="password" placeholder="Enter Password" required="required">
									</div>
								</div>
								<div class="col-12">
									<div class="form-group login-btn">
										<button class="btn rounded" type="submit">Login</button>
										<a href="register.php" class="btn rounded text-white">Register</a>
									</div>
									<div class="checkbox">
										<label class="checkbox-inline" for="2"><input name="news" id="2" type="checkbox">Remember me</label>
									</div>
									<a href="#" class="lost-pass">Lost your password?</a>
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
	<?php include('components/footer.php') ?>
	<!-- /End Footer Area -->

	<!-- Jquery -->
	<?php include('components/script.php') ?>
</body>

</html>