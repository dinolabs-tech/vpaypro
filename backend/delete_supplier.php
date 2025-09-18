<?php
// Include database connection
include './database/db_connection.php';

if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Delete the product from the database
    $stmt = $conn->prepare("DELETE FROM suppliers WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: suppliers.php?msg=deleted");
    } else {
        echo "Error deleting Supplier.";
    }
    $stmt->close();
} else {
    echo "Invalid request.";
}
?>