<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $post_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Check if the user has already liked the post
    $stmt = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // User has not liked the post, so add a like
        $stmt = $conn->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $post_id);
        if ($stmt->execute()) {
            // Update the likes count in the posts table
            $update_stmt = $conn->prepare("UPDATE posts SET likes = likes + 1 WHERE id = ?");
            $update_stmt->bind_param("i", $post_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
    }
    $stmt->close();
}

header("Location: blog_post_details.php?id=" . $post_id);
exit();
?>
