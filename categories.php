<?php
session_start();
// Ensure the user is logged in and is an admin
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once 'db_connect.php';

// Fetch all categories from the database
$result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
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
								<li class="active">Manage Categories</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Breadcrumbs -->
		
		<!-- Product Style 1 -->
		        <div class="container mt-2">
    <div class="row">
        <div class="col-md-12">
            <!-- <h2>Manage Categories</h2> -->
            <hr>
            <a href="add_category.php" class="btn btn-success mb-3 rounded text-white fa fa-plus px-3 py-2" style="font-size: 24px"></a>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>
<div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td>
                                    <a href="edit_category.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm rounded fa fa-edit px-3 mb-3 text-white" style="font-size: 18px; "></a>
                                    <a href="delete_category.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm rounded fa fa-trash px-3 mb-3 text-white" onclick="return confirm('Are you sure you want to delete this category?');" style="font-size: 18px;"></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">No categories found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
</div>
        </div>
    </div>
</div>
		<!--/ End Product Style 1  -->	
		<!-- Start Footer Area -->
	<?php include('components/footer.php')?>
		<!-- /End Footer Area -->
	
	
	<!-- Jquery -->
  <?php include('components/script.php')?>
</body>
</html>