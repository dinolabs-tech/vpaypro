<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include './database/db_connection.php';
session_start();


if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit();
}


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    header("Location: customer_orders.php");
    exit();
}

$order_id = (int)$_GET['id'];

// Fetch order details
$stmt = $conn->prepare("
    SELECT o.id, o.total_amount, o.status, o.order_date, o.delivery_address,
           d.delivery_personnel, l.staffname AS delivery_person_name, l.mobile AS mobile
    FROM orders o
    LEFT JOIN deliveries d ON o.id = d.order_id
    LEFT JOIN login l ON d.delivery_personnel = l.id
    WHERE o.id = ? AND o.customer_id = (SELECT customer_id FROM customers WHERE id = ?)
");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    die("Order not found or you do not have permission to view it.");
}

// Fetch order items
$order_items = [];
$stmt = $conn->prepare("SELECT oi.quantity, oi.price, p.productname FROM order_items oi JOIN product p ON oi.product_id = p.productid WHERE oi.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $order_items[] = $row;
}
$stmt->close();
?>
