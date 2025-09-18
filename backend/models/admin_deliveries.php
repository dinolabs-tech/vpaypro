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

// Handle delivery update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_delivery') {
    $delivery_id = (int)$_POST['delivery_id'];
    $status = $_POST['status'];
    $delivery_personnel = $_SESSION['user_id'];
    $estimated_delivery_date = $_POST['estimated_delivery_date'];
    
    $actual_delivery_date = ($status === 'delivered') ? date("Y-m-d H:i:s") : null;

    // Get the order ID from the delivery ID
    $stmt = $conn->prepare("SELECT order_id FROM deliveries WHERE id = ?");
    $stmt->bind_param("i", $delivery_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $delivery = $result->fetch_assoc();
    $order_id = $delivery['order_id'];
    $stmt->close();

    // Update deliveries table
    $stmt = $conn->prepare("UPDATE deliveries SET status = ?, delivery_personnel = ?, estimated_delivery_date = ?, actual_delivery_date = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $status, $delivery_personnel, $estimated_delivery_date, $actual_delivery_date, $delivery_id);
    $stmt->execute();
    $stmt->close();

    // Update orders table
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: admin_deliveries.php");
    exit();
}

$deliveries = [];
$user_country = $_SESSION['country'] ?? '';
$user_state = $_SESSION['state'] ?? '';
$sql = "
    SELECT d.id, d.order_id, d.status, u.staffname as delivery_personnel, d.estimated_delivery_date, d.actual_delivery_date, d.delivery_code, d.country, d.state 
    FROM deliveries d
    LEFT JOIN login u ON d.delivery_personnel = u.id
    WHERE d.country='$user_country' AND d.state='$user_state' 
    ORDER BY d.id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $deliveries[] = $row;
    }
}
?>
