<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: blog_post_details.php");
    exit();
}

include("db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment_id = $_POST["id"];
    $post_id = $_POST["post_id"];
    $comment = $_POST["comment"];

    $sql = "UPDATE comments SET content = '$comment' WHERE id = $comment_id";

    if ($conn->query($sql) === TRUE) {
        header("Location: blog_post_details.php?id=$post_id");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
