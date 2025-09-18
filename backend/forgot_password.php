<?php
require_once 'database/db_connection.php';
require_once 'email_sender.php'; // Include the email sender utility

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    $user = null;
    $table_name = '';
    $user_type = '';
    $name_column = '';

    // First, check in the 'login' table (staff)
    $stmt = $conn->prepare("SELECT id, staffname as name FROM login WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        $table_name = 'login';
        $user_type = 'staff';
        $name_column = 'staffname';
    } else {
        // If not found in 'login', check in the 'customers' table
        $stmt = $conn->prepare("SELECT id, name FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            $table_name = 'customers';
            $user_type = 'customer';
            $name_column = 'name';
        }
    }

    if ($user) {
        // Generate a unique token
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour')); // Token valid for 1 hour

        // Store the token and expiry in the appropriate database table
        $stmt = $conn->prepare("UPDATE {$table_name} SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
        $stmt->bind_param("ssi", $token, $expiry, $user['id']);
        $stmt->execute();
        $stmt->close();

        // Send password reset email
        $resetLink = "http://localhost/vpaypro/reset_password.php?token=" . $token . "&type=" . $user_type;
        $subject = ucfirst($user_type) . " Password Reset Request";
        $body = "Dear " . $user['name'] . ",<br><br>"
              . "You have requested to reset your password. Please click on the following link to reset your password:<br>"
              . "<a href='" . $resetLink . "'>" . $resetLink . "</a><br><br>"
              . "This link will expire in 1 hour. If you did not request a password reset, please ignore this email.<br><br>"
              . "Regards,<br>Dinolabs Team";

        if (sendEmail($email, $user['name'], $subject, $body)) {
            $message = "A password reset link has been sent to your email address.";
            $message_type = "success";
        } else {
            $message = "Failed to send password reset email. Please try again later.";
            $message_type = "danger";
        }
    } else {
        $message = "No user or customer found with that email address.";
        $message_type = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('components/head.php');?>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card mt-5">
                    <div class="card-body">
                        <h3 class="card-title text-center">Forgot Password</h3>
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>
                        <form action="forgot_password.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="index.php">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
