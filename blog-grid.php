<?php
session_start();
include('db_connect.php');
include('includes/functions.php');

?>

<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php')?>
<body class="js">

	
	<!-- Eshop Color Plate -->
	<div class="color-plate ">
		<a class="color-plate-icon"><i class="ti-paint-bucket"></i></a>
		<h4>Eshop Colors</h4>
		<p>Here is some awesome color's available on Eshop Template.</p>
		<span class="color1"></span>
		<span class="color2"></span>
		<span class="color3"></span>
		<span class="color4"></span>
		<span class="color5"></span>
		<span class="color6"></span>
		<span class="color7"></span>
		<span class="color8"></span>
		<span class="color9"></span>
		<span class="color10"></span>
		<span class="color11"></span>
		<span class="color12"></span>
	</div>
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
							<li><a href="index1.html">Home<i class="ti-arrow-right"></i></a></li>
							<li class="active"><a href="blog-single.php">Blog Grid</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->
			
	<!-- Start Blog Grid -->
	<div class="blog-single shop-blog grid section">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="row">
						<div class="col-lg-4 col-md-6 col-12">
							<!-- Start Single Blog  -->
							<div class="shop-single-blog">
								<img src="images" alt="#">
								<div class="content">
									<p class="date">22 July , 2020. Monday</p>
									<a href="#" class="title">Sed adipiscing ornare.</a>
									<a href="#" class="more-btn">Continue Reading</a>
								</div>
							</div>
							<!-- End Single Blog  -->
						</div>
						
						<div class="col-12">
							<!-- Pagination -->
							<div class="pagination center">
								<ul class="pagination-list">
									<li><a href="#"><i class="ti-arrow-left"></i></a></li>
									<li class="active"><a href="#">1</a></li>
									<li><a href="#">2</a></li>
									<li><a href="#">3</a></li>
									<li><a href="#"><i class="ti-arrow-right"></i></a></li>
								</ul>
							</div>
							<!--/ End Pagination -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--/ End Blog Grid -->
			
	<!-- Start Footer Area -->
<?php include('components/footer.php')?>
	<!-- /End Footer Area -->
	<?php include('components/script.php')?>
</body>
</html>