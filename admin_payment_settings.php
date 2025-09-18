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


$flutterwave_public_key = '';
$flutterwave_secret_key = '';
$message = '';

// Fetch existing settings
$stmt = $conn->prepare("SELECT setting_name, setting_value FROM payment_settings WHERE setting_name IN (?, ?)");
$public_key_name = 'flutterwave_public_key';
$secret_key_name = 'flutterwave_secret_key';
$stmt->bind_param('ss', $public_key_name, $secret_key_name);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if ($row['setting_name'] === 'flutterwave_public_key') {
        $flutterwave_public_key = $row['setting_value'];
    } elseif ($row['setting_name'] === 'flutterwave_secret_key') {
        $flutterwave_secret_key = $row['setting_value'];
    }
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_public_key = $_POST['flutterwave_public_key'] ?? '';
    $new_secret_key = $_POST['flutterwave_secret_key'] ?? '';

    // Update or insert public key
    $stmt = $conn->prepare("INSERT INTO payment_settings (setting_name, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->bind_param('sss', $public_key_name, $new_public_key, $new_public_key);
    $stmt->execute();
    $stmt->close();

    // Update or insert secret key
    $stmt = $conn->prepare("INSERT INTO payment_settings (setting_name, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->bind_param('sss', $secret_key_name, $new_secret_key, $new_secret_key);
    $stmt->execute();
    $stmt->close();

    $message = "Flutterwave settings updated successfully!";
    $flutterwave_public_key = $new_public_key;
    $flutterwave_secret_key = $new_secret_key;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include('components/head.php')?>
<body>
    <?php include('components/header.php')?>

    <div class="container" style="margin-top: 100px;">
        <h2>Flutterwave Payment Settings</h2>
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="post" action="admin_payment_settings.php">
            <div class="form-group">
                <label for="flutterwave_public_key">Flutterwave Public Key</label>
                <input type="text" class="form-control" id="flutterwave_public_key" name="flutterwave_public_key" value="<?php echo htmlspecialchars($flutterwave_public_key); ?>" required>
            </div>
            <div class="form-group">
                <label for="flutterwave_secret_key">Flutterwave Secret Key</label>
                <input type="text" class="form-control" id="flutterwave_secret_key" name="flutterwave_secret_key" value="<?php echo htmlspecialchars($flutterwave_secret_key); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary rounded">Save Settings</button>
        </form>
    </div>

    <?php include('components/footer.php')?>
    <?php include('components/script.php')?>
</body>
</html>
