<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/db_connection.php';

$customer_id = $_GET['customer_id'] ?? '';

if (empty($customer_id)) {
    echo json_encode(['success' => false, 'message' => 'Customer ID is required.']);
    exit;
}

$sql = "SELECT name, balance, profile_picture, account_status, country, state FROM customers WHERE customer_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
        if ($customer['account_status'] === 'disabled') {
            echo json_encode(['success' => false, 'message' => 'This account is disabled.']);
            exit;
        }
        echo json_encode([
            'success' => true,
            'name' => $customer['name'],
            'balance' => number_format($customer['balance'], 2),
            'profile_picture' => $customer['profile_picture'],
            'account_status' => $customer['account_status'],
            'country' => $customer['country'], // Added country
            'state' => $customer['state']      // Added state
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid Customer ID.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}

$conn->close();
?>
