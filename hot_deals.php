<?php
session_start();
include('db_connect.php');

// Pagination setup
$products_per_page = 9; // Number of products to display per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $products_per_page;

// Search and category filters (initialize to empty if not set)
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Hot deals query
$sql_hot_deal_count = "SELECT COUNT(*) AS total FROM product WHERE discount > NOW()";
$result_hot_deal_count = $conn->query($sql_hot_deal_count);
$total_product = $result_hot_deal_count->fetch_assoc()['total'];
$total_pages = ceil($total_product / $products_per_page);

$sql_hot_deal = "
    SELECT 
        p.*,
        (SELECT image_path FROM product_images WHERE product_id = p.productid LIMIT 1) as primary_image
    FROM product p
    WHERE p.discount > NOW()

    LIMIT $products_per_page OFFSET $offset";
$result_hot_deal = $conn->query($sql_hot_deal);

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
							<li class="active">Discounted Deals</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->

	<!-- Product Style 1 -->
	<section class="product-area shop-sidebar shop-list shop section">
		<div class="container">
			<div class="row">
			
				<div class="col-lg-9 col-md-8 col-12">
					<div class="row">
						<div class="col-12">
						
						</div>
					</div>
					<div class="row">
						<!-- Start Single List -->
						<!-- <div class="col-12"> -->
							<!-- <div class="row"> -->
								<?php foreach ($result_hot_deal as $product): ?>
									<div class="col-lg-4 col-md-6 col-sm-6">
										<div class="single-product">

											<div class="product-img">
												<a href="product_details.php">
													<?php $image_src = !empty($product['primary_image']) ? htmlspecialchars($product['primary_image']) : 'default.jpg'; ?>
													<img class="default-img img-fluid rounded" src="assets/images/<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="height:50vh; width:100vw">
													<img class="hover-img img-fluid rounded" src="assets/images/<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="height:50vh; width:100vw">
												</a>
												<div class="button-head">
													<div class="product-action">
														<a title="Wishlist" href="wishlist.php?add=<?php echo $product['id']; ?>"><i class=" ti-heart "></i><span>Add to Wishlist</span></a>
														<a title="Compare" href="compare.php?add=<?php echo $product['id']; ?>"><i class="ti-bar-chart-alt"></i><span>Compare</span></a>
													</div>
													<div class="product-action-2">
														<a title="Add to cart" href="cart.php?add=<?php echo $product['id']; ?>" class="btn rounded">Add to cart</a>
													</div>
												</div>
											</div>

										</div>
									<!-- </div> -->
									<!-- <div class="col-lg-8 col-md-6 col-12"> -->
										<div class="list-content">
											<div class="product-content">
												<h3 class="title"><?php echo $product['name']; ?></h3>
												<div class="product-price">
													<p class="discount"><small><s>$<?php echo $product['price']; ?></s></small></p>
													<p>$<?php echo $product['discount_price']; ?></p>
												</div>
											</div>
											<p class="des"><?php echo $product['description']; ?></p>
											<a href="product_details.php?id=<?= $product['id']; ?>" class="btn bg-dark rounded text-white">Read more...</a>
										</div>
									</div>
								<?php endforeach ?>
							<!-- </div> -->
						<!-- </div> -->
						<div class="col-12">
							<!-- Pagination -->
							<div class="pagination">
								<?php if ($total_pages > 1): ?>
									<nav aria-label="Page navigation">
										<ul class="pagination-list">
											<?php if ($page > 1): ?>
											<li class="page-item">
												<a class="page-link" href="hot_deals.php?page=<?php echo $page-1; ?><?php echo $search? '&search='.urlencode($search): ''; ?><?php echo $category? '&category='.urlencode($category): ''; ?>">Previous</a>
											</li>
											<?php endif; ?>
											<?php for ($i=1; $i<=$total_pages; $i++): ?>
											<li class="page-item <?php echo $i==$page? 'active':''; ?>">
												<a class="page-link" href="hot_deals.php?page=<?php echo $i; ?><?php echo $search? '&search='.urlencode($search): ''; ?><?php echo $category? '&category='.urlencode($category): ''; ?>"><?php echo $i; ?></a>
											</li>
											<?php endfor; ?>
											<?php if ($page < $total_pages): ?>
												<li class="page-item">
													<a class="page-link" href="hot_deals.php?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>">Next</a>
												</li>
											<?php endif; ?>
										</ul>
									<?php endif; ?>
							</div>
							<!--/ End Pagination -->
						</div>
					</div>
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
						<!-- <div class="single-widget recent-post"> -->
							<!-- <h3 class="title">Recent post</h3> -->
							<!-- Single Post -->
							<!-- <?php foreach ($result_hot_deal as $product): ?>
								<a href="product_details.php?id=<?= $product['id']; ?>">
									<div class="single-post first">
										<div class="image">
											<?php $image_src = !empty($product['primary_image']) ? htmlspecialchars($product['primary_image']) : 'default.jpg'; ?>
											<img class="img-fluid rounded-circle" src="assets/images/<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
										</div>
										<div class="content">
											<h5><a href="product_details.php?id=<?= $product['id']; ?>"><?php echo $product['name']; ?></a></h5>
											<p class="price"><?php echo $product['price']; ?></p>
											<ul class="reviews mb-3">

											</ul>
										</div>
									</div>
								</a>
							<?php endforeach ?> -->
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
