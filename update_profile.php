<?php
session_start();
require_once 'db_connect.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];

    // Prepare the SQL statement for updating user data
    $sql_update = "UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, contact = ?, address = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);

    // Bind parameters and execute the statement
    $stmt->bind_param('ssssssi', $first_name, $last_name, $username, $email, $contact, $address, $user_id);

    if ($stmt->execute()) {
        // Redirect back to profile page with a success message
        header("Location: profile.php?profile_updated=true");
        exit;
    } else {
        // Handle error if update fails
        header("Location: profile.php?profile_update_failed=true");
        exit;
    }
} else {
    // Redirect to profile page if accessed directly without POST request
    header("Location: profile.php");
    exit;
}
?>
