<?php
session_start();
include('db_connect.php');


require_once 'db_connect.php';

// Pagination variables
$products_per_page = 9; // Number of products to display per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $products_per_page;

// Query to get total count of products
$sql_total_products = "SELECT COUNT(*) as total_products FROM product";
$result_total_products = $conn->query($sql_total_products);
$total_products = $result_total_products->fetch_assoc()['total_products'];
$total_pages = ceil($total_products / $products_per_page);

if (isset($_GET['id'])) {
	$product_id = $_GET['id'];

	// Prepare statement to avoid SQL injection
	$stmt = $conn->prepare("SELECT * FROM product WHERE id = $product_id");
	$product = $conn->query($stmt);
}


// New product with pagination
$sql_new_product = " 
    SELECT 
        *
    FROM product 
    LIMIT $products_per_page OFFSET $offset";
$result_new_product = $conn->query($sql_new_product);

// Fetch categories
$sql_categories = "SELECT * FROM categories";
$result_categories = $conn->query($sql_categories);

?>
<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php') ?>

<body class="js">



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
							<li class="active">Products</a></li>
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
						<?php foreach ($result_new_product as $product): ?>
							<div class="col-lg-4 col-md-6 col-12">
								<div class="single-product">
									<div class="product-img">
										<a href="product_details.php?id=<?= $product['productid']; ?>">
											<?php $image_src = !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'assets/img/default.jpg'; ?>
											<img class="rounded img-fluid" src="backend/<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($product['productname']); ?>" style="height:50vh; width:100vw;">
										</a>
										<div class="button-head">
											<?php if (!isset($_SESSION['user_id'])) { ?>
												<div class="product-action-2">
													<a title="Add to Cart" href="backend/login.php" class="btn rounded">Add to Cart</a>
												</div>
											<?php } else { ?>
												<div class="product-action">
													<a title="Wishlist" href="wishlist.php?add=<?php echo $product['productid']; ?>"><i class=" ti-heart "></i><span>Add to Wishlist</span></a>
													<a title="Compare" href="compare.php?add=<?php echo $product['productid']; ?>"><i class="ti-bar-chart-alt"></i><span>Compare</span></a>
												</div>
												<div class="product-action-2">
													<a title="Add to Cart" href="cart.php?add=<?php echo $product['productid']; ?>" class="btn rounded">Add to Cart</a>
												</div>
											<?php } ?>
										</div>
									</div>
									<div class="product-content">
											<h3><a href="product_details.php?id=<?= $product['productid']; ?>"><?php echo $product['productname']; ?></a></h3>
										<div class="product-price">
											<span>$<?php echo $product['sellprice']; ?></span>
										</div>

									</div>
								</div>
							</div>
						<?php endforeach ?>

					</div>
					<!-- Start Pagination -->
					<div class="row mb-3">
						<div class="col-12">
							<div class="pagination justify-content-center center">
								<ul class="pagination-list">
									<?php if ($current_page > 1): ?>
										<li><a href="?page=<?php echo $current_page - 1; ?>"><i class="ti-arrow-left"></i></a></li>
									<?php endif; ?>

									<?php for ($i = 1; $i <= $total_pages; $i++): ?>
										<li class="<?php echo ($i == $current_page) ? 'active' : ''; ?>"><a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
									<?php endfor; ?>

									<?php if ($current_page < $total_pages): ?>
										<li><a href="?page=<?php echo $current_page + 1; ?>"><i class="ti-arrow-right"></i></a></li>
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
	<?php include('components/footer.php') ?>
	<!-- /End Footer Area -->


	<!-- Jquery -->
	<?php include('components/script.php') ?>
</body>

</html>