<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


include 'database/db_connection.php';
session_start();


// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle user deletion
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Prepare and execute the delete statement
    $sql = "DELETE FROM login WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            // Redirect back to the users page with a success message
            header("Location: users.php?msg=deleted");
            exit();
        } else {
            // Handle deletion error
            echo "<p style='color:red;'>Error deleting user: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color:red;'>Error preparing delete statement: " . $conn->error . "</p>";
    }
} else {
    // If no ID is provided or method is not GET
    header("Location: users.php");
    exit();
}

$conn->close();
?>
