<?php
session_start();
include('db_connect.php');
include('backend/database_schema.php');
include('includes/functions.php');

// Pagination variables
$product_per_page = 9; // Number of products to display per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $product_per_page;

// Query to get total count of products
$sql_total_product = "SELECT COUNT(*) as total_product FROM product";
$result_total_product = $conn->query($sql_total_product);
$total_product = $result_total_product->fetch_assoc()['total_product'];
$total_pages = ceil($total_product / $product_per_page);

if (isset($_GET['id'])) {
	$product_id = $_GET['id'];

	// Prepare statement to avoid SQL injection
	$stmt = $conn->prepare("SELECT * FROM product WHERE id = $product_id");
	$product = $conn->query($stmt);
}


// New product with pagination
$sql_new_product = " 
    SELECT 
        p.*,
		AVG(r.rating) as avg_rating
    FROM product p
	LEFT JOIN reviews r ON p.productid = r.product_id
    WHERE p.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY p.productid
    ORDER BY p.created_at DESC
    LIMIT $product_per_page OFFSET $offset";
$result_new_product = $conn->query($sql_new_product);




?>

<!DOCTYPE html>
<html lang="eng">
<?php require_once 'components/head.php'; ?>

<body class="js">



	<!-- Header -->
	<?php require_once 'components/header.php'; ?>
	<!--/ End Header -->

	<!-- Start Area 2 -->
	<!-- <section class="hero-area4">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="home-slider-4">
						<div class="big-content" style="background-image: url('images/home/cloth1.jpg');">
							<div class="inner">
								<h4 class="title">make your <br> site stunning with <br> large title</h4>
								<p class="des">Hipster style is a fashion trending for Gentleman and Lady<br>with tattoos. You’ll become so cool and attractive with your’s girl.<br> Now let come hare and grab it now !</p>
								<div class="button">
									<a href="#" class="btn">Shop Now</a>
								</div>
							</div>
						</div>
						<div class="big-content" style="background-image: url('images/home/cloth9.jpg');">
							<div class="inner">
								<h4 class="title">make your <br> site stunning with <br> large title</h4>
								<p class="des">Hipster style is a fashion trending for Gentleman and Lady<br>with tattoos. You’ll become so cool and attractive with your’s girl.<br> Now let come hare and grab it now !</p>
								<div class="button">
									<a href="#" class="btn">Shop Now</a>
								</div>
							</div>
						</div>
						<div class="big-content" style="background-image: url('images/home/cloth8.jpg');">
							<div class="inner">
								<h4 class="title">make your <br> site stunning with <br> large title</h4>
								<p class="des">Hipster style is a fashion trending for Gentleman and Lady<br>with tattoos. You’ll become so cool and attractive with your’s girl.<br> Now let come hare and grab it now !</p>
								<div class="button">
									<a href="#" class="btn">Shop Now</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section> -->
	<!--/ End Hero Area 2 -->

	<!-- Start Small Banner  -->
	<!-- <section class="small-banner section"> -->
	<!-- <div class="container"> -->
	<!-- <div class="row"> -->
	<!-- Single Banner  -->
	<!-- <div class="col-lg-4 col-md-6 col-12">
					<div class="single-banner">
						<img src="images/home/cloth7.jpg" alt="#">
						<div class="content">
							<p>Man's Collectons</p>
							<h3>Summer travel <br> collection</h3>
							<a href="#">Discover Now</a>
						</div>
					</div>
				</div> -->
	<!-- /End Single Banner  -->
	<!-- Single Banner  -->
	<!-- <div class="col-lg-4 col-md-6 col-12">
					<div class="single-banner">
						<img src="images/home/cloth6.jpg" alt="#">
						<div class="content">
							<p>Bag Collectons</p>
							<h3>Awesome Bag <br> 2020</h3>
							<a href="#">Shop Now</a>
						</div>
					</div>
				</div> -->
	<!-- /End Single Banner  -->
	<!-- Single Banner  -->
	<!-- <div class="col-lg-4 col-12">
					<div class="single-banner tab-height">
						<img src="images/home/cloth5.jpg" alt="#">
						<div class="content">
							<p>Flash Sale</p>
							<h3>Mid Season <br> Up to <span>40%</span> Off</h3>
							<a href="#">Discover Now</a>
						</div>
					</div>
				</div> -->
	<!-- /End Single Banner  -->
	<!-- </div> -->
	<!-- </div> -->
	<!-- </section> -->
	<!-- End Small Banner -->

	<!-- Start Product Area -->
	<div class="product-area section">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="section-title">
						<h2>All Products</h2>
					</div>
				</div>
			</div>
			<div class="row">

				<div class="col-lg-12 col-md-8 col-12">
					<div class="row">
						<?php foreach ($result_new_product as $product): ?>
							<div class="col-lg-3 col-md-6 col-12">
								<div class="single-product">
									<div class="product-img">
										<a href="product_details.php?id=<?= $product['productid']; ?>">
											<?php $image_src = !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'assets/img/default.jpg'; ?>
											<img class="rounded img-fluid" src="backend/<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($product['productname']); ?>" style="height:50vh; width:100vw;">
										</a>
										<div class="button-head">
											<?php if (!isset($_SESSION['user_id'])) { ?>
												<div class="product-action-2">
													<a title="Add to Cart" href="backend/index.php" class="btn rounded">Add to Cart</a>
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
											<span>&#8358; <?php echo $product['sellprice']; ?></span>
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
			</div>
		</div>
	</div>
	<!-- End Product Area -->

	<!-- Start New Products -->
	<div class="product-area most-popular section">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="section-title">
						<h2>New Products</h2>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="owl-carousel popular-slider">
						<?php foreach ($result_new_product as $product): ?>
							<!-- Start Single Product -->
							<div class="single-product">
								<div class="product-img">
									<a href="product_details.php?id=<?php echo $product['productid']; ?>">
										<?php $image_src = !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'assets/img/default.jpg'; ?>
										<img class="default-img rounded img-fluid" src="backend/<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($product['productname']); ?>">
										<img class="hover-img rounded img-fluid" src="backend/<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($product['productname']); ?>">
										<?php if ($product['discount'] > 0): ?>
											<span class="out-of-stock">
												<?php
												// $percentage_reduction = (($product['price'] - $product['discount_price']) / $product['price']) * 100;

												$discounted_value = (($product['sellprice'] - ($product['discount'] / 100) * $product['sellprice']));
												// $percentage_reduction = (($product['sellprice'] - ($discounted_value / $product['sellprice'] * 100)));
												$percentage_reduction = $product['discount'];
												echo '<p class="percentage-reduction text-white">' . round($percentage_reduction) . '%</p>';
												?>
											</span>
										<?php else: ?>
											<span class="out-of-stock">Hot</span>
										<?php endif; ?>
									</a>
									<div class="button-head">
										<div class="product-action">
											<a title="Wishlist" href="wishlist.php?add=<?php echo $product['productid']; ?>"><i class=" ti-heart "></i><span>Add to Wishlist</span></a>
											<a title="Compare" href="compare.php?add=<?php echo $product['productid']; ?>"><i class="ti-bar-chart-alt"></i><span>Add to Compare</span></a>
										</div>
										<div class="product-action-2">
											<a title="Add to cart" href="cart.php?add=<?php echo $product['productid']; ?>">Add to Cart</a>
										</div>
									</div>
								</div>
								<div class="product-content">
									<h3><a href="product_details.php?id=<?php echo $product['productid']; ?>"><?php echo $product['productname']; ?></a></h3>
									<div class="product-price">
										<div class="rating"><?php echo generate_stars($product['avg_rating']); ?></div>
										<?php if ($product['discount'] > 0): ?>
											<?php $discounted_value = ($product['discount'] / 100) * $product['sellprice'];
													$discount = $product['sellprice'] - $discounted_value;
											?>
											<span class="discount-price">&#8358; <?php echo $discount; ?></span>
											<span class="original-price"><i><small class="text-danger"><s>&#8358; <?php echo $product['sellprice']; ?></s></small></i></span>
										<?php else: ?>
											<p>&#8358; <?php echo $product['sellprice']; ?></p>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<!-- End Single Product -->
						<?php endforeach ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End New Products Area -->


	<!-- Start Shop Blog  -->
	<section class="shop-blog section">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="section-title">
						<h2>From Our Blog</h2>
					</div>
				</div>
			</div>

			<div class='row'>
				<?php
				$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
				$posts_per_page = 3;
				$offset = ($page - 1) * $posts_per_page;

				$sql_trending_posts = "SELECT posts.id, posts.title, posts.created_at, posts.image_path, COUNT(comments.id) AS total_comments FROM posts LEFT JOIN comments ON posts.id = comments.post_id GROUP BY posts.id ORDER BY total_comments DESC LIMIT $posts_per_page OFFSET $offset";
				$result_trending_posts = $conn->query($sql_trending_posts);

				$sql_total_posts = "SELECT COUNT(*) AS total FROM posts";
				$result_total_posts = $conn->query($sql_total_posts);
				$total_posts = $result_total_posts->fetch_assoc()['total'];
				$total_pages = ceil($total_posts / $posts_per_page);
				if ($result_trending_posts->num_rows > 0) {
					while ($trending_post = $result_trending_posts->fetch_assoc()) {
						echo "<div class='col-lg-4 col-md-6 col-12'>";

						echo "<!-- Start Single Blog  -->";
						echo "<div class='shop-single-blog'>";


						if ($trending_post['image_path']) {
							echo "      <img class='img-fluid rounded' src='assets/images/" . $trending_post['image_path'] . "' style='width: 400px; height: 250px; object-fit: cover;'>";
						} else {
							echo "      <img class='img-fluid rounded' src='img/default.jpg' style='width: 400px; height: 250px; object-fit: cover;'>"; // fallback image
						}
						echo "<div class='content'>";
						echo "          <small>Created: " . $trending_post['created_at'] . "</small><br>";
						echo "          <h4 class='mb-1'>" . htmlspecialchars($trending_post['title']) . "</h4>";
						echo "      <a href='blog_post_details.php?id=" . $trending_post['id'] . "' class='more-btn'>Continue Reading</a> ";
						echo "</div>";
						echo "</div>";

						echo "<!-- End Single Blog  -->";

						echo "</div>";
					}
				} else {
					echo "<p>No trending posts found.</p>";
				}
				?>
			</div>

		</div>
	</section>
	<!-- End Shop Blog  -->

	<!-- Start Shop Services Area -->
	<section class="shop-services section home">
		<div class="container">
			<div class="row">
				<div class="col-lg-3 col-md-6 col-12">
					<!-- Start Single Service -->
					<div class="single-service">
						<i class="ti-rocket"></i>
						<h4>Free shiping</h4>
						<p>Orders over $100</p>
					</div>
					<!-- End Single Service -->
				</div>
				<div class="col-lg-3 col-md-6 col-12">
					<!-- Start Single Service -->
					<div class="single-service">
						<i class="ti-reload"></i>
						<h4>Free Return</h4>
						<p>Within 30 days returns</p>
					</div>
					<!-- End Single Service -->
				</div>
				<div class="col-lg-3 col-md-6 col-12">
					<!-- Start Single Service -->
					<div class="single-service">
						<i class="ti-lock"></i>
						<h4>Sucure Payment</h4>
						<p>100% secure payment</p>
					</div>
					<!-- End Single Service -->
				</div>
				<div class="col-lg-3 col-md-6 col-12">
					<!-- Start Single Service -->
					<div class="single-service">
						<i class="ti-tag"></i>
						<h4>Best Peice</h4>
						<p>Guaranteed price</p>
					</div>
					<!-- End Single Service -->
				</div>
			</div>
		</div>
	</section>
	<!-- End Shop Services Area -->







	<!-- Start Footer Area -->
	<?php require_once 'components/footer.php'; ?>
	<!-- /End Footer Area -->

	<?php require_once 'components/script.php'; ?>
</body>

</html>