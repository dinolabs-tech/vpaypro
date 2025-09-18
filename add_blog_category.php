<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
include("db_connect.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $description = $_POST["description"];

    $sql = "INSERT INTO blog_categories (name, description) VALUES ('$name', '$description')";

    if ($conn->query($sql) === TRUE) {
        header("Location: blog_categories.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php')?>
<body class="js">
	
	<!-- Eshop Color Plate -->

	<!-- /End Color Plate -->
		
		<!-- Header -->
<?php include('components/header.php')?>
		<!--/ End Header -->
	
		<!-- Breadcrumbs -->
		<div class="breadcrumbs">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<div class="bread-inner">
							<ul class="bread-list">
								<li><a href="index.php">Home<i class="ti-arrow-right"></i></a></li>
								<li class="active">Add Blog Category</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Breadcrumbs -->

        <div class="container pt-3">
            <div class="form-main">
                <form action="add_blog_category.php" method="post">
                    <div class="row">
                        <div class="col-12">
                                <input type="text" class="form-control px-2" placeholder="Category Name"   id="name" name="name">
                            </div><br><br>
                            <div class="col-12">
                                <textarea class="form-control" placeholder="Description" id="description" name="description"></textarea>
                            </div><br><br>
                            <div class="col-12 mt-3">
                                <button class="btn rounded fa fa-plus mb-3 px-5 text-white" style="font-size: 18px" type="submit"></button>
                            </div>
                    </div>
            </div>
        </div>