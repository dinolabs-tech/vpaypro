<?php
session_start();
include('db_connect.php');

// Pagination variables for New Products
$new_product_per_page = 9; // Number of new products to display per page
$current_new_product_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$new_product_offset = ($current_new_product_page - 1) * $new_product_per_page;

// Query to get total count of new products
$sql_total_new_product = "SELECT COUNT(*) as total_product FROM product WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$result_total_new_product = $conn->query($sql_total_new_product);
$total_new_product = $result_total_new_product->fetch_assoc()['total_product'];
$total_new_product_pages = ceil($total_new_product / $new_product_per_page);

// New product with pagination
$sql_new_product = " 
    SELECT * FROM product
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ORDER BY created_at DESC
    LIMIT $new_product_per_page OFFSET $new_product_offset";
$result_new_product = $conn->query($sql_new_product);

// Fetch categories (assuming this is needed for the sidebar)
$sql_categories = "SELECT * FROM categories";
$result_categories = $conn->query($sql_categories);


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
								<li class="active">New Products</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Breadcrumbs -->
		
		<!-- Product Style -->
		<section class="product-area shop-sidebar shop section">
			<div class="container">
				<div class="row">
					
					<div class="col-lg-9 col-md-8 col-12">
						<div class="row">
							<div class="col-12">
								
							</div>
						</div>
						<div class="row">
							<?php foreach ($result_new_product as $product): ?>
							<div class="col-lg-4 col-md-6 col-12">
								<div class="single-product">
									
									<div class="product-img">
										<a href="product_details.php?id=<?= $product['productid']; ?>">
											<?php $image_src = !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'assets/img/default.jpg'; ?>
											<img class="default-img rounded img-fluid" src="backend/<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($product['productname']); ?>" style="height:50vh; width:100vw">
											<img class="hover-img rounded img-fluid"src="backend/<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($product['productname']); ?>" style="height:50vh; width:100vw">
										</a>
										<div class="button-head">
											<div class="product-action">`
												<a title="Wishlist"href="wishlist.php?add=<?php echo $product['productid']; ?>"><i class=" ti-heart "></i><span>Add to Wishlist</span></a>
												<a title="Compare" href="compare.php?add=<?php echo $product['productid']; ?>"><i class="ti-bar-chart-alt"></i><span>Add to Compare</span></a>
											</div>
											<div class="product-action-2">
												<a title="Add to cart"  href="cart.php?add=<?php echo $product['productid']; ?>" class="btn">Add to cart</a>
											</div>
										</div>
									</div>
									
									<div class="product-content">
										<h3><a href="product_details.php"><?php echo $product['productname']; ?></a></h3>
										<div class="product-price">
											<p>$<?php echo $product['sellprice']; ?></p>
										</div>
									</div>
								</div>
							</div>
							<?php endforeach?>
							
						</div>
						<!-- Start Pagination -->
						<div class="row mb-3">
							<div class="col-12">
								<div class="pagination justify-content-center center">
									<ul class="pagination-list">
										<?php if ($current_new_product_page > 1): ?>
											<li><a href="?page=<?php echo $current_new_product_page - 1; ?>"><i class="ti-arrow-left"></i></a></li>
										<?php endif; ?>

										<?php for ($i = 1; $i <= $total_new_product_pages; $i++): ?>
											<li class="<?php echo ($i == $current_new_product_page) ? 'active' : ''; ?>"><a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
										<?php endfor; ?>

										<?php if ($current_new_product_page < $total_new_product_pages): ?>
											<li><a href="?page=<?php echo $current_new_product_page + 1; ?>"><i class="ti-arrow-right"></i></a></li>
										<?php endif; ?>
									</ul>
								</div>
							</div>
						</div>
						<!-- End Pagination -->
					</div>

					<div class="col-lg-3 col-md-4 col-12">
					<div class="shop-sidebar">
						<!-- Single Widget -->
						<div class="single-widget category">
							<h3 class="title">Categories</h3>
							<ul class="categor-list">
								<?php foreach ($result_categories as $category): ?>
									<a href="product_category.php?category_id=<?php echo $category['id']; ?>">
										<option value="products.php?category_id=<?= $category['id']; ?>"><?= $category['name']; ?></option>
									</a>
								<?php endforeach; ?>
							</ul>
						</div>
						<!--/ End Single Widget -->
					
						<!-- Single Widget -->
						<div class="single-widget recent-post">
							<h3 class="title">Recent Products</h3>
							<!-- Single Post -->
							<?php foreach ($result_new_product as $product): ?>
								<a href="product_details.php?id=<?= $product['productid']; ?>">
									<div class="single-post first">
										<div class="image">
											<?php $image_src = !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'assets/img/default.jpg'; ?>
											<img class="img-fluid rounded-circle" src="backend/<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($product['productname']); ?>">
										</div>
										<div class="content">
											<h5><a href="product_details.php?id=<?= $product['productid']; ?>"><?php echo $product['productname']; ?></a></h5>
											<p class="price"><?php echo $product['sellprice']; ?></p>
											<ul class="reviews mb-3">

											</ul>
										</div>
									</div>
								</a>
							<?php endforeach ?>
							<!-- End Single Post -->
						</div>
						<!--/ End Single Widget -->
					</div>
				</div>
				</div>
			</div>
		</section>
		<!--/ End Product Style 1  -->	
		<!-- Start Footer Area -->
	<?php include('components/footer.php')?>
		<!-- /End Footer Area -->
	
	
    <!-- Jquery -->
<?php include('components/script.php')?>
</body>
</html>
