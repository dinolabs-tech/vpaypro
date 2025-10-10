<?php
session_start();
require_once 'backend/database/db_connection.php';
require_once 'backend/phpmailer/PHPMailerAutoload.php'; // For sending emails
require_once 'backend/email_sender.php'; // Custom email sender functions

// Initialize an error message variable
$error_message = '';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email']; // Assuming customers log in with email
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $customer = $result->fetch_assoc();
        // Verify the submitted password against the hashed password from the database
        if (password_verify($password, $customer['password'])) {
            // Password is correct, generate 2FA code
            $two_factor_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $two_factor_expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes')); // Code valid for 10 minutes

            // Store 2FA code and expiration in the database
            $update_stmt = $conn->prepare("UPDATE customers SET two_factor_code = ?, two_factor_expires_at = ? WHERE id = ?");
            $update_stmt->bind_param("ssi", $two_factor_code, $two_factor_expires_at, $customer['id']);
            $update_stmt->execute();
            $update_stmt->close();

            // Send 2FA code to the customer's email
            $subject = "Your 2FA Verification Code";
            $body = "Your 2FA verification code is: <strong>" . $two_factor_code . "</strong>. It will expire in 10 minutes.";
            sendEmail($customer['email'], $customer['name'], $subject, $body);

            // Set session variable to indicate 2FA is pending
            $_SESSION['2fa_customer_id'] = $customer['id'];
            $_SESSION['2fa_email'] = $customer['email'];

            // Redirect to backend login page with 2FA action
            header("Location: backend/index.php?action=verify_2fa");
            exit;
        } else {
            // Set error message for incorrect password
            $error_message = "Incorrect password";
        }
    } else {
        // Set error message for incorrect email
        $error_message = "Incorrect email or password";
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
										<label>Your Email<span>*</span></label>
										<input type="email" name="email" placeholder="Enter Email" required="required">
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
									<a href="forgot_password.php" class="lost-pass">Lost your password?</a>
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
