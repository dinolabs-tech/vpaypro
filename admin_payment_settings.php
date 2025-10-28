<?php
session_start();
require_once 'db_connect.php';
require_once 'functions.php'; // Assuming functions.php contains utility functions like check_admin_role()

// Basic authorization check (replace with a more robust solution if needed)
// if (!is_admin()) { // Assuming a function is_admin() exists in functions.php
//     header("Location: login.php");
//     exit;
// }

// For now, a simple check if a user is logged in and has a role_id (assuming admin is role_id 1)
if (!isset($_SESSION['username']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 0)) {
    header("Location: login.php");
    exit;
}


$message = '';
// The Flutterwave settings are now managed in backend/payment_gateways.php
// This file will no longer handle Flutterwave specific keys.
?>

<!DOCTYPE html>
<html lang="en">
<?php include('components/head.php')?>
<body>
    <?php include('components/header.php')?>

    <div class="container" style="margin-top: 100px;">
        <h2>Payment Settings</h2>
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <p>Payment gateway settings are now managed in the <a href="payment_gateways.php">Payment Gateways</a> section.</p>
        <!-- You can add other general payment settings here if needed -->
    </div>

    <?php include('components/footer.php')?>
    <?php include('components/script.php')?>
</body>
</html>
