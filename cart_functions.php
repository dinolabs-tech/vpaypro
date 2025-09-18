<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function add_to_cart($product_id, $quantity) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product && $quantity > 0) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantity
            ];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    add_to_cart($product_id, $quantity);
    header('Location: ' . BASE_URL . '?page=cart');
    exit;
}
?>