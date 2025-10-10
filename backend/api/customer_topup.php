<?php
<<<<<<< HEAD

=======
>>>>>>> 605bfc311f4172e6b5396bd450f4df50af4fedf7

include '../database/db_connection.php';
session_start();
// Get transaction details from query parameters
$customerId = $_GET['customer_id'] ?? null;
$transactionAmount = $_GET['amount'] ?? null;
$transactionRef = $_GET['tx_ref'] ?? null;
$transactionStatus = $_GET['status'] ?? null;

// Validate received data
if (!$customerId || !$transactionAmount || !$transactionRef || !$transactionStatus) {
    // Handle error: missing data
    http_response_code(400);
    echo "Error: Missing transaction details.";
    exit;
}

// Ensure the customer is logged in and the session ID matches the provided customer ID
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $customerId) {
    http_response_code(401); // Unauthorized
    echo "Error: User authentication failed or session expired.";
    exit;
}

// Fetch the actual customer_id from the customers table using the session ID
$stmt_get_customer_id = $conn->prepare('SELECT customer_id FROM customers WHERE id = ?');
if (!$stmt_get_customer_id) {
    error_log("MySQL Prepare Error (fetch customer_id): " . $conn->error);
    http_response_code(500);
    echo "Database error retrieving customer ID.";
    exit;
}
$stmt_get_customer_id->bind_param('i', $_SESSION['user_id']);
$stmt_get_customer_id->execute();
$result_get_customer_id = $stmt_get_customer_id->get_result();

if ($result_get_customer_id->num_rows === 0) {
    error_log("Customer ID not found for session ID: " . $_SESSION['user_id']);
    http_response_code(404);
    echo "Customer not found.";
    $stmt_get_customer_id->close();
    exit;
}

$customer_data = $result_get_customer_id->fetch_assoc();
$actualCustomerId = $customer_data['customer_id']; // This is the VARCHAR customer_id
$stmt_get_customer_id->close();


if ($transactionStatus === 'successful') {
    // Update customer balance
    $stmt = $conn->prepare('UPDATE customers SET balance = balance + ? WHERE id = ?');
    if (!$stmt) {
        error_log("MySQL Prepare Error (update balance in customer_topup.php): " . $conn->error);
        http_response_code(500);
        echo "Database error updating balance.";
        exit;
    }
<<<<<<< HEAD
    // Use $_SESSION['id'] for updating the balance as it refers to the customer's primary key 'id'
=======
    // Use $_SESSION['user_id'] for updating the balance as it refers to the customer's primary key 'id'
>>>>>>> 605bfc311f4172e6b5396bd450f4df50af4fedf7
    $stmt->bind_param('di', $transactionAmount, $_SESSION['user_id']);
    if (!$stmt->execute()) {
        error_log("MySQL Execute Error (update balance in customer_topup.php): " . $stmt->error);
        http_response_code(500);
        echo "Database error executing balance update.";
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Record transaction
    $stmt = $conn->prepare('INSERT INTO customer_transactions (customer_id, transaction_type, amount) VALUES (?, ?, ?)');
    if (!$stmt) {
        error_log("MySQL Prepare Error (insert transaction in customer_topup.php): " . $conn->error);
        http_response_code(500);
        echo "Database error recording transaction.";
        exit;
    }
    $transaction_type = 'funding';
    // Use the fetched actualCustomerId (VARCHAR) for the foreign key constraint
    $stmt->bind_param('ssd', $actualCustomerId, $transaction_type, $transactionAmount);
    if (!$stmt->execute()) {
        error_log("MySQL Execute Error (insert transaction in customer_topup.php): " . $stmt->error);
        http_response_code(500);
        echo "Database error executing transaction insert.";
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Set success message in session
    $_SESSION['success_message'] = 'Top-up successful! Your balance has been updated.';
    // Redirect back to the dashboard to show updated balance
    header('Location: ../customer_dashboard.php');
    exit;

} else {
    // Transaction was not successful, redirect to dashboard with a message or handle failure
    // For now, redirecting to dashboard. A more robust solution might show an error message.
    header('Location: customer_dashboard.php?status=failed&message=Transaction+was+not+successful.');
    exit;
}
?>
