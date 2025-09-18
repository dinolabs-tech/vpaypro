<?php
session_start();
// if (!isset($_SESSION["username"]) || !isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != true) {
//     header("Location: blog.php");
//     exit();
// }

include("db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $post_id = $_GET["id"];

    // Delete likes associated with the post
    $sql_likes = "DELETE FROM likes WHERE post_id = $post_id";
    $conn->query($sql_likes);

    // Delete comments associated with the post
    $sql_comments = "DELETE FROM comments WHERE post_id = $post_id";
    $conn->query($sql_comments);

    // Fetch image filename
    $sql = "SELECT image_path FROM posts WHERE id = $post_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image_filename = $row["image_path"];

        // Delete the post
        $sql = "DELETE FROM posts WHERE id = $post_id";

        if ($conn->query($sql) === TRUE) {
            // Delete the image file
            if (!empty($image_filename) && file_exists("assets/images/" . $image_filename)) {
                unlink("assets/images/" . $image_filename);
            }

            header("Location: blog.php");
            exit();
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        echo "Post not found";
    }
}

$conn->close();
