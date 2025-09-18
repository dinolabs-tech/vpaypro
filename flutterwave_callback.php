<?php
session_start();
require_once 'db_connect.php';
require_once 'cart_functions.php'; // To clear the cart
require_once 'functions.php'; // For any utility functions

// Fetch Flutterwave API secret key
$flutterwave_secret_key = '';
$stmt = $conn->prepare("SELECT setting_value FROM payment_settings WHERE setting_name = ?");
$secret_key_name = 'flutterwave_secret_key';
$stmt->bind_param('s', $secret_key_name);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $flutterwave_secret_key = $row['setting_value'];
}
$stmt->close();

if (empty($flutterwave_secret_key)) {
    die("Flutterwave API secret key is not configured. Please contact the administrator.");
}

// It's important to verify the webhook signature in a real application
// For this example, we'll focus on verifying the transaction after redirection

if (isset($_GET['status']) && isset($_GET['tx_ref']) && isset($_GET['transaction_id'])) {
    $status = $_GET['status'];
    $tx_ref = $_GET['tx_ref'];
    $transaction_id = $_GET['transaction_id'];

    if ($status === 'successful') {
        // Verify the transaction with Flutterwave
        $verify_url = "https://api.flutterwave.com/v3/transactions/{$transaction_id}/verify";
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $flutterwave_secret_key,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $verify_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $response_data = json_decode($response, true);

        if ($http_code == 200 && $response_data['status'] === 'success') {
            $transaction_details = $response_data['data'];

            // Check if the transaction reference matches what we stored
            // And if the amount and currency match
            if ($transaction_details['tx_ref'] === $_SESSION['flutterwave_tx_ref'] &&
                $transaction_details['amount'] >= $_SESSION['flutterwave_amount'] && // Use >= for safety
                $transaction_details['currency'] === $_SESSION['flutterwave_currency']) {

                // Payment is verified and successful
                // Now, insert the order into the database
                $customer_name = $_SESSION['flutterwave_customer_name'] ?? 'Guest';
                $customer_email = $_SESSION['flutterwave_customer_email'] ?? 'guest@example.com';
                $total = $_SESSION['flutterwave_amount'] ?? 0;

                $conn->begin_transaction();

                try {
                    // Insert into orders table
                    $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, total, payment_status, transaction_id) VALUES (?, ?, ?, ?, ?)");
                    $payment_status = 'completed';
                    $stmt->bind_param('ssdss', $customer_name, $customer_email, $total, $payment_status, $transaction_id);
                    $stmt->execute();
                    $order_id = $stmt->insert_id;

                    // Insert into order_items table
                    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                    foreach ($_SESSION['cart'] as $product_id => $item) {
                        $stmt->bind_param('iiid', $order_id, $product_id, $item['quantity'], $item['price']);
                        $stmt->execute();
                    }

                    $conn->commit();

                    // Clear the cart and Flutterwave session data
                    $_SESSION['cart'] = [];
                    unset($_SESSION['flutterwave_tx_ref']);
                    unset($_SESSION['flutterwave_amount']);
                    unset($_SESSION['flutterwave_currency']);
                    unset($_SESSION['flutterwave_customer_email']);
                    unset($_SESSION['flutterwave_customer_name']);
                    unset($_SESSION['flutterwave_customer_phone']);
                    unset($_SESSION['flutterwave_customer_address']);

                    header("Location: profile.php?order_success=true");
                    exit;

                } catch (Exception $e) {
                    $conn->rollback();
                    die("Order placement failed after successful payment: " . $e->getMessage());
                }

            } else {
                // Mismatch in transaction details
                die("Transaction verification failed: Mismatch in details.");
            }
        } else {
            // Flutterwave verification failed
            die("Flutterwave transaction verification failed: " . ($response_data['message'] ?? 'Unknown error'));
        }
    } else {
        // Payment was not successful (e.g., cancelled, failed)
        // Clear relevant session data
        unset($_SESSION['flutterwave_tx_ref']);
        unset($_SESSION['flutterwave_amount']);
        unset($_SESSION['flutterwave_currency']);
        unset($_SESSION['flutterwave_customer_email']);
        unset($_SESSION['flutterwave_customer_name']);
        unset($_SESSION['flutterwave_customer_phone']);
        unset($_SESSION['flutterwave_customer_address']);

        header("Location: checkout.php?payment_status=failed&message=" . urlencode($status));
        exit;
    }
} else {
    // Invalid callback
    header("Location: checkout.php?payment_status=invalid_callback");
    exit;
}
?>
