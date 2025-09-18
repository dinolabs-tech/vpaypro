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

if (!isset($_GET['id'])) {
    header("Location: admin_orders.php");
    exit();
}

$order_id = (int)$_GET['id'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_order_details.php?id=" . $order_id);
    exit();
}


// Fetch order details
$sql = "SELECT o.id, o.customer_id, o.total_amount, o.status, o.order_date, o.delivery_address, c.name, c.email, c.phone, c.country, c.state FROM orders o JOIN customers c ON o.customer_id = c.customer_id WHERE o.id = ?";
$params = ["i", $order_id];

if ($_SESSION['role'] !== 'Administrator' && $_SESSION['role'] !== 'Superuser') {
    $sql .= " AND c.country = ?";
    $params[0] .= "s"; // Add 's' for string type for country
    $params[] = $_SESSION['country'];
}

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$bind_params = [];
$bind_params[] = $params[0]; // The type string

for ($i = 1; $i < count($params); $i++) {
    $bind_params[] = &$params[$i]; // Pass subsequent parameters by reference
}
call_user_func_array([$stmt, 'bind_param'], $bind_params);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    die("Order not found.");
}

$customer = [
    'name' => $order['name'],
    'email' => $order['email'],
    'phone' => $order['phone'],
    'country' => $order['country'],
    'state' => $order['state']
];

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
