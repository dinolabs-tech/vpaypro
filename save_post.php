<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include("db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST["title"]);
    $content = $conn->real_escape_string($_POST["content"]);
    $category_id = (int) $_POST["category"];
    $author_id = $_SESSION["user_id"]; 

    $uploadOk = 1;
    $target_dir = "assets/images/";
    $image_path = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_path;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image is actual image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["image"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowedTypes = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowedTypes)) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // If everything is OK, upload the file and insert post
    if ($uploadOk === 1) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Insert post into DB
            $sql = "INSERT INTO posts (title, content, author_id, category_id, image_path) 
                    VALUES ('$title', '$content', '$author_id', '$category_id', '$image_path')";

            if ($conn->query($sql) === TRUE) {
                header("Location: blog.php");
                exit();
            } else {
                echo "Database Error: " . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "File upload failed. Post not saved.";
    }
}

$conn->close();
?>
