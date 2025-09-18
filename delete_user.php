<?php
session_start();
require_once 'db_connect.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

header('Location: users.php');
exit;
?>