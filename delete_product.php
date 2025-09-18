<?php
session_start();
require_once 'db_connect.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$product_id = $_GET['id'];
// First, delete related records from tables that have foreign key constraints
// For example, from 'cart', 'wishlist', 'order_items', 'reviews'
$stmt_cart = $conn->prepare("DELETE FROM cart WHERE product_id = ?");
$stmt_cart->bind_param("i", $product_id);
$stmt_cart->execute();
$stmt_cart->close();

$stmt_wishlist = $conn->prepare("DELETE FROM wishlist WHERE product_id = ?");
$stmt_wishlist->bind_param("i", $product_id);
$stmt_wishlist->execute();
$stmt_wishlist->close();

$stmt_order_items = $conn->prepare("DELETE FROM order_items WHERE product_id = ?");
$stmt_order_items->bind_param("i", $product_id);
$stmt_order_items->execute();
$stmt_order_items->close();

$stmt_reviews = $conn->prepare("DELETE FROM reviews WHERE product_id = ?");
$stmt_reviews->bind_param("i", $product_id);
$stmt_reviews->execute();
$stmt_reviews->close();

// Now, delete the product itself
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->close();

header('Location: products.php');
exit;
?>