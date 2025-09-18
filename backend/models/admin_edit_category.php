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

if (!isset($_GET['id'])) {
    header("Location: admin_categories.php");
    exit();
}

$category_id = (int)$_GET['id'];

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_category') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $description, $category_id);
    if ($stmt->execute()) {
        $message = "Category updated successfully.";
    } else {
        $message = "Error updating category: " . $conn->error;
    }
    $stmt->close();
}

// Fetch category details
$stmt = $conn->prepare("SELECT id, name, description FROM categories WHERE id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();
$stmt->close();

if (!$category) {
    die("Category not found.");
}
?>
