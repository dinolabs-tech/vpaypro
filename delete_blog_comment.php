<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: blog_post_details.php");
    exit();
}

include("db_connect.php");

$comment_id = $_GET["id"];

$sql = "SELECT * FROM comments WHERE id = $comment_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Comment not found";
    exit();
}

$comment = $result->fetch_assoc();
$post_id = $comment["post_id"];

$sql = "DELETE FROM comments WHERE id = $comment_id";

if ($conn->query($sql) === TRUE) {
    header("Location: blog_post_details.php?id=$post_id");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    }

$conn->close();
?>