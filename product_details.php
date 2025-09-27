<?php
session_start();
include('db_connect.php');
include('includes/functions.php');

$product = null;
if (isset($_GET['id'])) {
	$product_id = $_GET['id'];
	$stmt = $conn->prepare("
    SELECT p.*, c.name AS category_name, bpi.quantity, AVG(r.rating) AS avg_rating 
    FROM product p 
    LEFT JOIN reviews r ON p.productid = r.product_id 
    LEFT JOIN product_categories pc ON p.productid = pc.product_id
    LEFT JOIN categories c ON pc.category_id = c.id 
	LEFT JOIN branch_product_inventory bpi ON p.productid = bpi.productid
    WHERE p.productid = ? 
    GROUP BY p.productid
");
	$stmt->bind_param("i", $product_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$product = $result->fetch_assoc();
	$stmt->close();

	// $product_images = [];
	// $stmt_images = $conn->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
	// $stmt_images->bind_param("i", $product_id);
	// $stmt_images->execute();
	// $result_images = $stmt_images->get_result();
	// while ($row = $result_images->fetch_assoc()) {
	// 	$product_images[] = $row['image_path'];
	// }
	// $stmt_images->close();
}

if (!$product) {
	// Redirect to home or show an error if product not found
	header("Location: index.php");
	exit();
}

$reviews = [];
$stmt_reviews = $conn->prepare("
    SELECT r.*, c.name AS customer_name, l.staffname AS staff_name 
    FROM reviews r 
    LEFT JOIN customers c ON r.user_id = c.id 
    LEFT JOIN login l ON r.user_id = l.id 
    WHERE r.product_id = ? 
    ORDER BY r.created_at DESC
");

$stmt_reviews->bind_param("i", $product_id);
$stmt_reviews->execute();
$result_reviews = $stmt_reviews->get_result();
while ($row = $result_reviews->fetch_assoc()) {
	$reviews[] = $row;
}
$stmt_reviews->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
	if (isset($_SESSION['user_id'])) {
		$user_id = $_SESSION['user_id'];
		$rating = $_POST['rating'];
		$review_text = $conn->real_escape_string($_POST['review_text']);

		$stmt_insert_review = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)");
		$stmt_insert_review->bind_param("iiis", $product_id, $user_id, $rating, $review_text);
		$stmt_insert_review->execute();
		$stmt_insert_review->close();

		// Redirect to prevent form resubmission
		header("Location: product_details.php?id=" . $product_id);
		exit();
	}
}

?>

<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php') ?>

<body class="js">



	<!-- Header -->
	<?php include('components/header.php'); ?>
	<!--/ End Header -->

	<!-- Breadcrumbs -->
	<div class="breadcrumbs">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="bread-inner">
						<ul class="bread-list">
							<li><a href="index.php">Home<i class="ti-arrow-right"></i></a></li>
							<li class="active">Product Details</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->

	<!-- Shop Single -->
	<section class="shop single section">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="row">
						<div class="col-lg-8 col-12">
							<!-- Product Slider -->
							<div class="product-gallery">
								<!-- Images slider -->
								<div class="flexslider-thumbnails">
									<ul class="slides">
										<?php if (!empty($product['image_url'])): ?>

											<li data-thumb="backend/<?php echo htmlspecialchars($product['image_url']); ?>">
												<img class="rounded img-fluid" src="backend/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['productname']); ?>" style="height:50vh; width:100vw;">
											</li>

										<?php else: ?>
											<li data-thumb="assets/img/default.jpg">
												<img class="rounded" src="assets/img/default.jpg" alt="Default Image">
											</li>
										<?php endif; ?>
									</ul>
								</div>
								<!-- End Images slider -->
							</div>
							<!-- End Product slider -->
						</div>
						<div class="col-lg-4 col-12">
							<div class="product-des">
								<!-- Description -->
								<div class="short">
									<h4><?php echo htmlspecialchars($product['productname']); ?></h4>
									<div class="rating-main">
										<?php echo generate_stars($product['avg_rating']); ?>
									</div>
									<?php if ($product['discount'] > 0): ?>
										<p><strong>Price: <span class="discount">
													<?php $discounted_value = ($product['discount'] / 100) * $product['sellprice'];
													$discount = $product['sellprice'] - $discounted_value;
													?>
													&#8358; <?php echo htmlspecialchars($discount); ?></strong></span> <small><s class="original-price">&#8358; <?php echo htmlspecialchars($product['sellprice']); ?></s></small></p>
										<?php
										$percentage_reduction = $product['discount'];
										echo '<p class="percentage-reduction"><strong>' . round($percentage_reduction) . '% off</strong></p>';
										?>

									<?php else: ?>
										<p><strong>Price:</strong> &#8358; <?php echo htmlspecialchars($product['sellprice']); ?></p>
									<?php endif; ?>
									<p class="description"><strong>Description: </strong> <?php echo htmlspecialchars($product['description']); ?></p>
								</div>
								<!--/ End Description -->
								<!-- Color -->

								<!--/ End Color -->
								<!-- Size -->
								<!-- <div class="size">
									<h4>Size</h4>
									<ul>
										<li><a href="#" class="one">S</a></li>
										<li><a href="#" class="two">M</a></li>
										<li><a href="#" class="three">L</a></li>
										<li><a href="#" class="four">XL</a></li>
										<li><a href="#" class="four">XXL</a></li>
									</ul>
								</div> -->
								<!--/ End Size -->
								<!-- Product Buy -->
								<div class="product-buy">
									<?php if (!isset($_SESSION['user_id'])) { ?>
										<a href="backend/index.php" class="btn rounded text-white">Add to cart</a>
									<?php } else { ?>
										<div class="add-to-cart">
											<a href="cart.php?add=<?php echo htmlspecialchars($product['productid']); ?>" class="btn rounded text-white">Add to cart</a>
											<a href="wishlist.php?add=<?php echo htmlspecialchars($product['productid']); ?>" class="btn min rounded text-white"><i class="ti-heart"></i></a>
											<a href="compare.php?add=<?php echo htmlspecialchars($product['productid']); ?>" class="btn min rounded text-white"><i class="fa fa-compress"></i></a>
										</div>
									<?php } ?>
									<p class="cat">Category :<a href="#"><?php echo htmlspecialchars($product['category_name']); ?></a></p>
									<p class="availability">Availability : <?php echo htmlspecialchars($product['quantity']); ?></p>
								</div>
								<!--/ End Product Buy -->
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<div class="product-info">
								<div class="nav-main">
									<!-- Tab Nav -->
									<ul class="nav nav-tabs" id="myTab" role="tablist">
										<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#description" role="tab">Description</a></li>
										<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#reviews" role="tab">Reviews</a></li>
									</ul>
									<!--/ End Tab Nav -->
								</div>
								<div class="tab-content" id="myTabContent">
									<!-- Description Tab -->
									<div class="tab-pane fade show active" id="description" role="tabpanel">
										<div class="tab-single">
											<div class="row">
												<div class="col-12">
													<div class="single-des">
														<p><?php echo htmlspecialchars($product['description']); ?></p>
													</div>

												</div>
											</div>
										</div>
									</div>
									<!--/ End Description Tab -->

									<!-- Reviews Tab -->
									<div class="tab-pane fade" id="reviews" role="tabpanel">
										<div class="tab-single review-panel">
											<div class="row">
												<div class="col-12">
													<div class="ratting-main">
														<div class="avg-ratting">
															<?php
															$t_review = array_sum(array_column($reviews, 'rating'));
															?>
															<?php if (count($reviews) > 0): ?>
																<h4><?= htmlspecialchars(round($t_review / count($reviews))) ?> <span>(Ratings Overall)</span></h4>
																<span>Based on <?= htmlspecialchars(count($reviews)) ?> Comments</span>
															<?php else: ?>
																<h4>0 <span>(Ratings Overall)</span></h4>
																<span>Based on 0 Comments</span>
															<?php endif; ?>
														</div>

														<?php
														if (count($reviews) > 0) {
															foreach ($reviews as $review) { ?>
																<!-- Single Rating -->
																<div class="single-rating">
																	<!-- <div class="rating-author">
																		<img src="assets/images/default.jpg" alt="#">
																	</div> -->
																	<div class="rating-des">
																		<p><strong><?= htmlspecialchars($review['customer_name'] ?: $review['staff_name']) ?> </strong></p>
																		<div class="ratings">
																			<ul class="rating">
																				<?php
																				$rating = floatval($review['rating']);
																				for ($i = 1; $i <= 5; $i++) {
																					if ($rating >= $i) {
																						echo '<li><i class="fa fa-star"></i></li>'; // full star
																					} elseif ($rating >= $i - 0.5) {
																						echo '<li><i class="fa fa-star-half-o"></i></li>'; // half star
																					} else {
																						echo '<li><i class="fa fa-star-o"></i></li>'; // empty star
																					}
																				}
																				?>
																			</ul>
																			<div class="rate-count">(<span><?= number_format($rating, 1) ?></span>)</div>
																		</div>
																		<p><?= htmlspecialchars($review['review_text']) ?> </p>
																	</div>
																</div>
																<!--/ End Single Rating -->
															<?php    } ?>
														<?php } else { ?>
															<p>No reviews yet. Be the first to review this product!</p>
														<?php } ?>

													</div>
													<!-- Review -->
													<div class="comment-review">
														<div class="add-review">
															<h5>Add A Review</h5>
														</div>
														<h4>Your Rating</h4>
														<div class="review-inner">
															<?php if (isset($_SESSION['user_id'])): ?>
																<div class="ratings">
																	<form class="form" action="product_details.php?id=<?php echo $product_id; ?>" method="post">
																		<div class="row">
																			<select class="form-control form-select mx-3" name="rating" id="rating" required>
																				<option value="5">5 Stars</option>
																				<option value="4">4 Stars</option>
																				<option value="3">3 Stars</option>
																				<option value="2">2 Stars</option>
																				<option value="1">1 Star</option>
																			</select><br><br>


																			<div class="col-lg-12 col-12">
																				<div class="form-group mt-3">
																					<textarea name="review_text" id="review_text" rows="6" placeholder="Write a Review" required></textarea>
																				</div>
																			</div>

																			<button type="submit" class="btn rounded" name="submit_review">Submit Review</button>
																		</div>
																	</form>
																</div>
															<?php else: ?>
																<p>Please <a href="login.php">log in</a> to submit a review.</p>
															<?php endif; ?>
														</div>
													</div>
													<!--/ End Review -->

												</div>
											</div>
										</div>
									</div>
									<!--/ End Reviews Tab -->
								</div>
								<!--/ End Reviews Tab -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>
	</section>
	<!--/ End Shop Single -->

	<!-- Start Most Popular -->
	<!-- <div class="product-area most-popular related-product section">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="section-title">
						<h2>Related Products</h2>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="owl-carousel popular-slider">

						
						<div class="single-product">
							<div class="product-img">
								<a href="product_details.php">
									<img class="default-img" src="https://via.placeholder.com/550x750" alt="#">
									<img class="hover-img" src="https://via.placeholder.com/550x750" alt="#">
									<span class="out-of-stock">Hot</span>
								</a>
								<div class="button-head">
									<div class="product-action">
										<a data-toggle="modal" data-target="#exampleModal" title="Quick View" href="#"><i class=" ti-eye"></i><span>Quick Shop</span></a>
										<a title="Wishlist" href="#"><i class=" ti-heart "></i><span>Add to Wishlist</span></a>
										<a title="Compare" href="#"><i class="ti-bar-chart-alt"></i><span>Add to Compare</span></a>
									</div>
									<div class="product-action-2">
										<a title="Add to cart" href="#">Add to cart</a>
									</div>
								</div>
							</div>
							<div class="product-content">
								<h3><a href="product_details.php">Black Sunglass For Women</a></h3>
								<div class="product-price">
									<span class="old">$60.00</span>
									<span>$50.00</span>
								</div>
							</div>
						</div>
						
						
						<div class="single-product">
							<div class="product-img">
								<a href="product_details.php">
									<img class="default-img" src="https://via.placeholder.com/550x750" alt="#">
									<img class="hover-img" src="https://via.placeholder.com/550x750" alt="#">
								</a>
								<div class="button-head">
									<div class="product-action">
										<a data-toggle="modal" data-target="#exampleModal" title="Quick View" href="#"><i class=" ti-eye"></i><span>Quick Shop</span></a>
										<a title="Wishlist" href="#"><i class=" ti-heart "></i><span>Add to Wishlist</span></a>
										<a title="Compare" href="#"><i class="ti-bar-chart-alt"></i><span>Add to Compare</span></a>
									</div>
									<div class="product-action-2">
										<a title="Add to cart" href="#">Add to cart</a>
									</div>
								</div>
							</div>
							<div class="product-content">
								<h3><a href="product_details.php">Women Hot Collection</a></h3>
								<div class="product-price">
									<span>$50.00</span>
								</div>
							</div>
						</div>
					
						
						<div class="single-product">
							<div class="product-img">
								<a href="product_details.php">
									<img class="default-img" src="https://via.placeholder.com/550x750" alt="#">
									<img class="hover-img" src="https://via.placeholder.com/550x750" alt="#">
									<span class="new">New</span>
								</a>
								<div class="button-head">
									<div class="product-action">
										<a data-toggle="modal" data-target="#exampleModal" title="Quick View" href="#"><i class=" ti-eye"></i><span>Quick Shop</span></a>
										<a title="Wishlist" href="#"><i class=" ti-heart "></i><span>Add to Wishlist</span></a>
										<a title="Compare" href="#"><i class="ti-bar-chart-alt"></i><span>Add to Compare</span></a>
									</div>
									<div class="product-action-2">
										<a title="Add to cart" href="#">Add to cart</a>
									</div>
								</div>
							</div>
							<div class="product-content">
								<h3><a href="product_details.php">Awesome Pink Show</a></h3>
								<div class="product-price">
									<span>$50.00</span>
								</div>
							</div>
						</div>
				
						
						<div class="single-product">
							<div class="product-img">
								<a href="product_details.php">
									<img class="default-img" src="https://via.placeholder.com/550x750" alt="#">
									<img class="hover-img" src="https://via.placeholder.com/550x750" alt="#">
								</a>
								<div class="button-head">
									<div class="product-action">
										<a data-toggle="modal" data-target="#exampleModal" title="Quick View" href="#"><i class=" ti-eye"></i><span>Quick Shop</span></a>
										<a title="Wishlist" href="#"><i class=" ti-heart "></i><span>Add to Wishlist</span></a>
										<a title="Compare" href="#"><i class="ti-bar-chart-alt"></i><span>Add to Compare</span></a>
									</div>
									<div class="product-action-2">
										<a title="Add to cart" href="#">Add to cart</a>
									</div>
								</div>
							</div>
							<div class="product-content">
								<h3><a href="product_details.php">Awesome Bags Collection</a></h3>
								<div class="product-price">
									<span>$50.00</span>
								</div>
							</div>
						</div>
						
						
					</div>
				</div>
			</div>
		</div>
	</div> -->
	<!-- End Most Popular Area -->

	<!-- Modal -->
	<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="ti-close" aria-hidden="true"></span></button>
				</div>
				<div class="modal-body">
					<div class="row no-gutters">
						<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
							<!-- Product Slider -->
							<div class="product-gallery">
								<div class="quickview-slider-active">
									<div class="single-slider">
										<img src="https://via.placeholder.com/569x528" alt="#">
									</div>
									<div class="single-slider">
										<img src="https://via.placeholder.com/569x528" alt="#">
									</div>
									<div class="single-slider">
										<img src="https://via.placeholder.com/569x528" alt="#">
									</div>
									<div class="single-slider">
										<img src="https://via.placeholder.com/569x528" alt="#">
									</div>
								</div>
							</div>
							<!-- End Product slider -->
						</div>
						<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
							<div class="quickview-content">
								<h2>Flared Shift Dress</h2>
								<div class="quickview-ratting-review">
									<div class="quickview-ratting-wrap">
										<div class="quickview-ratting">
											<i class="yellow fa fa-star"></i>
											<i class="yellow fa fa-star"></i>
											<i class="yellow fa fa-star"></i>
											<i class="yellow fa fa-star"></i>
											<i class="fa fa-star"></i>
										</div>
										<a href="#"> (1 customer review)</a>
									</div>
									<div class="quickview-stock">
										<span><i class="fa fa-check-circle-o"></i> in stock</span>
									</div>
								</div>
								<h3>$29.00</h3>
								<div class="quickview-peragraph">
									<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Mollitia iste laborum ad impedit pariatur esse optio tempora sint ullam autem deleniti nam in quos qui nemo ipsum numquam.</p>
								</div>
								<div class="size">
									<div class="row">
										<div class="col-lg-6 col-12">
											<h5 class="title">Size</h5>
											<select>
												<option selected="selected">s</option>
												<option>m</option>
												<option>l</option>
												<option>xl</option>
											</select>
										</div>
										<div class="col-lg-6 col-12">
											<h5 class="title">Color</h5>
											<select>
												<option selected="selected">orange</option>
												<option>purple</option>
												<option>black</option>
												<option>pink</option>
											</select>
										</div>
									</div>
								</div>
								<div class="quantity">
									<!-- Input Order -->
									<div class="input-group">
										<div class="button minus">
											<button type="button" class="btn btn-primary btn-number" disabled="disabled" data-type="minus" data-field="quant[1]">
												<i class="ti-minus"></i>
											</button>
										</div>
										<input type="text" name="quant[1]" class="input-number" data-min="1" data-max="1000" value="1">
										<div class="button plus">
											<button type="button" class="btn btn-primary btn-number" data-type="plus" data-field="quant[1]">
												<i class="ti-plus"></i>
											</button>
										</div>
									</div>
									<!--/ End Input Order -->
								</div>
								<div class="add-to-cart">
									<a href="#" class="btn">Add to cart</a>
									<a href="#" class="btn min"><i class="ti-heart"></i></a>
									<a href="#" class="btn min"><i class="fa fa-compress"></i></a>
								</div>
								<div class="default-social">
									<h4 class="share-now">Share:</h4>
									<ul>
										<li><a class="facebook" href="#"><i class="fa fa-facebook"></i></a></li>
										<li><a class="twitter" href="#"><i class="fa fa-twitter"></i></a></li>
										<li><a class="youtube" href="#"><i class="fa fa-pinterest-p"></i></a></li>
										<li><a class="dribbble" href="#"><i class="fa fa-google-plus"></i></a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal end -->

	<!-- Start Footer Area -->
	<?php include('components/footer.php') ?>
	<!-- /End Footer Area -->

	<!-- Jquery -->
	<?php include('components/script.php') ?>
</body>

</html>