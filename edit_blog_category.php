<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once 'db_connect.php';


$category_id = $_GET['id'];
if (!$category_id) {
    header("Location: manage_categories.php");
    exit;
}

// Fetch the category details
$stmt = $conn->prepare("SELECT * FROM blog_categories WHERE id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();
$stmt->close();

if (!$category) {
    $_SESSION['error_message'] = "Category not found.";
    header("Location: edit_blog_category.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    if (!empty($name)) {
        $stmt = $conn->prepare("UPDATE blog_categories SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $category_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Category updated successfully.";
            header("Location: blog_categories.php");
            exit;
        } else {
            $error_message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Category name is required.";
    }
}
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
								<li><a href="manage_categories.php">Manage Blog Categories<i class="ti-arrow-right"></i></a></li>
                                <li class="active">Edit Category</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Breadcrumbs -->

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- <h2>Edit Category</h2> -->
            <hr>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form action="edit_blog_category.php?id=<?php echo $category_id; ?>" method="post">
                <div class="form-group">
                    <label for="name">Category Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary rounded text-white">Update Category</button>
                <a href="blog_categories.php" class="btn btn-secondary rounded text-white">Cancel</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>

	<!-- Start Footer Area -->
<?php include('components/footer.php');?>
	<!-- /End Footer Area -->
	
	
 <!-- scripts goes here -->
  <?php include('components/scripts.php');?>
