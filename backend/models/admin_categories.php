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

$message = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'add_category':
            $name = $_POST['name'];
            $description = $_POST['description'];
            $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $description);
            if ($stmt->execute()) {
                $message = "Category added successfully.";
            } else {
                $message = "Error adding category: " . $conn->error;
            }
            $stmt->close();
            break;

        case 'delete_category':
            $category_id = (int)$_POST['category_id'];
            // First, remove associations in product_categories
            $stmt = $conn->prepare("DELETE FROM product_categories WHERE category_id = ?");
            $stmt->bind_param("i", $category_id);
            $stmt->execute();
            $stmt->close();

            // Then, delete the category
            $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->bind_param("i", $category_id);
            if ($stmt->execute()) {
                $message = "Category deleted successfully.";
            } else {
                $message = "Error deleting category: " . $conn->error;
            }
            $stmt->close();
            break;
    }
}

// Fetch all categories
$categories = [];
$sql = "SELECT id, name FROM categories ORDER BY name ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>
