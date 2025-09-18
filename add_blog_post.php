<?php
session_start();
if (!isset($_SESSION["user_id"])) {
	header("Location: index.php");
	exit();
}

include("db_connect.php");
?>

<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>

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
							<li><a href="blog.php">Post<i class="ti-arrow-right"></i></a></li>
							<li class="active">Add new post</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->

	<div class="container pt-3">
		<div class="form-main">
			<form action="save_post.php" method="post" enctype="multipart/form-data" class="row g-4" novalidate>

				<div class="col-12">
					<input type="text" class="form-control px-2" id="title" name="title" placeholder="Post Title" required>
				</div><br><br>

				<div class="col-12">
					<textarea class="form-control" id="content" name="content" placeholder="Content" rows="6"></textarea>
				</div>

				<div class="col-12">
					<label for="image" class="form-label text-white">Upload Image</label>
					<input type="file" class="form-control" id="image" name="image" required>
				</div>

				<div class="col-12"><br>
					<select class="form-select form-control" id="category" name="category" required>
						<option value="" selected disabled>Select Category</option>
						<?php
						$sql_categories = "SELECT * FROM blog_categories";
						$result_categories = $conn->query($sql_categories);
						if ($result_categories->num_rows > 0) {
							while ($row_category = $result_categories->fetch_assoc()) { ?>
								<option value='<?php echo $row_category["id"] ?>'><?php echo htmlspecialchars($row_category["name"]) ?></option>
							<?php } ?>
						<?php } else { ?>
							<option value=''>No categories available</option>
						<?php }
						?>
					</select>
				</div>

				<div class="col-12 text-center">
					<button type="submit" class="btn rounded fa fa-save mb-3 px-5 text-white" style="font-size: 18px"></button>
				</div>

			</form>
		</div>
	</div>

	<script>
		tinymce.init({
			selector: '#content',
			menubar: false,
			toolbar: 'undo redo | formatselect | bold italic underline superscript subscript | alignleft aligncenter alignright | bullist numlist outdent indent | table',
			plugins: 'lists',
			branding: false
		});

		document.querySelector('form').addEventListener('submit', function(e) {
			if (tinymce.get('content').getContent({
					format: 'text'
				}).trim() === '') {
				alert('Please enter some content.');
				e.preventDefault();
			}
		});
	</script>
	<?php include('components/footer.php') ?>
	<?php include('components/script.php') ?>
</body>

</html>