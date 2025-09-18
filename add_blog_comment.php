<?php
session_start();
include("db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = $_POST["post_id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $comment = $_POST["comment"];

    $sql = "INSERT INTO comments (post_id, name, email, content) VALUES ($post_id, '$name', '$email', '$comment')";

    if ($conn->query($sql) === TRUE) {
        header("Location: blog_post_details.php?id=$post_id");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
