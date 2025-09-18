<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}
include("db_connect.php");

$sql = "SELECT * FROM blog_categories";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php') ?>

<body class="js">


	<!-- Eshop Color Plate -->
	<!-- /End Color Plate -->

	<!-- Header -->
	<?php include('components/header.php') ?>
	<!--/ End Header -->

	<!-- Breadcrumbs -->
	<div class="breadcrumbs">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="bread-inner">
						<ul class="bread-list">
							<li><a href="index.php">Home<i class="ti-arrow-right"></i></a></li>
							<li class="active">Manage Blog Categories</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->

	<!-- Content Start -->
        <div class="container">
            <div class="col-md-12 mx-0">
                <a href="add_blog_category.php" class="btn rounded mb-3 px-3 fa fa-plus text-white" style="font-size: 24px"></a>
            </div>

            <div class="row g-5 mx-0">
                <div class="col-lg-12 wow slideInUp" data-wow-delay="0.3s">
                    <div class="table-responsive">
                        <table id="categoryTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <td>ID</td>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row["id"]) ?></td>
                                            <td><?= htmlspecialchars($row["name"]) ?></td>
                                            <td><?= htmlspecialchars($row["description"]) ?></td>
                                            <td>
                                                <a href='edit_blog_category.php?id=<?= $row["id"] ?>' class='btn rounded mb-3 px-3 fa fa-edit text-white' style="font-size: 18px"></a>
                                                <a href='delete_blog_category.php?id=<?= $row["id"] ?>' class='btn rounded mb-3 px-3 fa fa-trash text-white' style="font-size: 18px" onclick='return confirm("Are you sure you want to delete this category?")'></a>
                                            </td>
                                        </tr>
                                    <?php   } ?>
                                <?php   } ?>


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
	<!--/ End Blog Grid -->

	<!-- Start Footer Area -->
	<?php include('components/footer.php') ?>
	<!-- /End Footer Area -->
	<?php include('components/script.php') ?>
</body>

</html>