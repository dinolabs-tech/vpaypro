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

$orders = [];
$stmt = $conn->prepare("SELECT id, total_amount, status, order_date FROM orders WHERE customer_id = (SELECT customer_id FROM customers WHERE id = ?) ORDER BY order_date DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
$stmt->close();
?>
