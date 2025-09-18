<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include './database/db_connection.php';
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_settings') {
    $delivery_fee = $_POST['delivery_fee'];
    $currency = $_POST['currency'];
    $language = $_POST['language'];

    // Update or insert delivery fee
    $stmt_delivery = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'delivery_fee'");
    if ($stmt_delivery === false) {
        // Handle error, e.g., log it or set an error message
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        $message = "Error updating delivery fee.";
    } else {
        $stmt_delivery->bind_param("s", $delivery_fee);
        if (!$stmt_delivery->execute()) {
            error_log("Execute failed: (" . $stmt_delivery->errno . ") " . $stmt_delivery->error);
            $message = "Error updating delivery fee.";
        }
        $stmt_delivery->close();
    }

    // Update or insert currency
    $stmt_currency = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'currency'");
    if ($stmt_currency === false) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        $message = ($message ? $message . " " : "") . "Error updating currency.";
    } else {
        $stmt_currency->bind_param("s", $currency);
        if (!$stmt_currency->execute()) {
            error_log("Execute failed: (" . $stmt_currency->errno . ") " . $stmt_currency->error);
            $message = ($message ? $message . " " : "") . "Error updating currency.";
        }
        $stmt_currency->close();
    }

    // Update or insert language
    $stmt_language = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'language'");
    if ($stmt_language === false) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        $message = ($message ? $message . " " : "") . "Error updating language.";
    } else {
        $stmt_language->bind_param("s", $language);
        if (!$stmt_language->execute()) {
            error_log("Execute failed: (" . $stmt_language->errno . ") " . $stmt_language->error);
            $message = ($message ? $message . " " : "") . "Error updating language.";
        }
        $stmt_language->close();
    }

    if (empty($message) || strpos($message, "Error") === false) {
        $message = "Settings updated successfully.";
    }
}

// Fetch current settings
$settings = [];
$setting_keys = ['delivery_fee', 'currency', 'language'];

foreach ($setting_keys as $key) {
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $settings[$key] = $result->fetch_assoc()['setting_value'];
    } else {
        // Set default values if setting does not exist
        switch ($key) {
            case 'delivery_fee':
                $settings[$key] = 0;
                break;
            case 'currency':
                $settings[$key] = 'NGN'; // Default currency
                break;
            case 'language':
                $settings[$key] = 'en'; // Default language
                break;
            default:
                $settings[$key] = '';
        }
        // Optionally, insert default values into the database if they don't exist
        $stmt_insert = $conn->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt_insert->bind_param("ss", $key, $settings[$key]);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
    $stmt->close();
}

// Assign fetched settings to variables for use in admin_settings.php
$delivery_fee = $settings['delivery_fee'];
$currency = $settings['currency'];
$language = $settings['language'];

?>
