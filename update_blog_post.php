<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: blog_post_details.php");
    exit();
}

include("db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = intval($_POST["id"]);
    $title = mysqli_real_escape_string($conn, $_POST["title"]);
    $content = mysqli_real_escape_string($conn, $_POST["content"]);
    $category_id = intval($_POST["category"]);

    $image_path = ""; // Will store new image name if uploaded

    // Handle image upload
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "assets/images/";
        $image_path = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_path;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file
        $uploadOk = 1;
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }
        if ($_FILES["image"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk) {
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                echo "Sorry, there was an error uploading your file.";
                $image_path = ""; // reset if failed
            }
        } else {
            $image_path = ""; // reset if validation failed
        }
    }

    // Build SQL
    if (!empty($image_path)) {
        $sql = "UPDATE posts 
                SET title = '$title', content = '$content', category_id = '$category_id', image_path = '$image_path' 
                WHERE id = $post_id";
    } else {
        $sql = "UPDATE posts 
                SET title = '$title', content = '$content', category_id = '$category_id' 
                WHERE id = $post_id";
    }

    // Execute update
    if ($conn->query($sql) === TRUE) {
        header("Location: blog.php");
        exit();
    } else {
        echo "Error updating post: " . $conn->error;
    }
}

$conn->close();
?>