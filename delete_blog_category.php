<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once 'db_connect.php';

if (isset($_GET['id'])) {
    $category_id = $_GET['id'];
    
    // Check if there are any products associated with this category
    $stmt = $conn->prepare("SELECT COUNT(*) as product_count FROM products WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $product_count = $row['product_count'];
    $stmt->close();

    if ($product_count > 0) {
        $_SESSION['error_message'] = "Cannot delete the category because it has products associated with it.";
        header("Location: blog_categories.php");
        exit;
    }

    // Proceed with deletion if no products are associated
    $stmt = $conn->prepare("DELETE FROM blog_categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Category deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting category: " . $stmt->error;
    }
    $stmt->close();
}

header("Location: blog_categories.php");
exit;
?>