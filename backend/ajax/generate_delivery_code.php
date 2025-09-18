<?php
include '../database/db_connection.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    $user_id = (int)$_SESSION['user_id'];

    // Generate a unique delivery code
    $delivery_code = 'DEL-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

    // Update the delivery status and code
    $actual_delivery_date = date("Y-m-d H:i:s");
    $status = 'delivered';

    // Update deliveries table
    $stmt = $conn->prepare("UPDATE deliveries SET status = ?, actual_delivery_date = ?, delivery_code = ? WHERE order_id = ?");
    $stmt->bind_param("sssi", $status, $actual_delivery_date, $delivery_code, $order_id);
    $stmt->execute();
    $stmt->close();

    // Update orders table
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'delivery_code' => $delivery_code]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update delivery status.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
?>
