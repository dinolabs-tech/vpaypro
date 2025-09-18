<?php
session_start();
include('db_connect.php');
include('includes/functions.php');


// Fetch Blog categories
$sql_blog_categories = "SELECT * FROM blog_categories";
$result_blog_categories = $conn->query($sql_blog_categories);
?>

<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php') ?>

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
	<?php include('components/header.php') ?>
	<!--/ End Header -->

	<!-- Breadcrumbs -->
	<div class="breadcrumbs">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="bread-inner">
						<ul class="bread-list">
							<li><a href="index1.html">Home<i class="ti-arrow-right"></i></a></li>
							<li class="active"><a href="blog-single.html">Blog Single Sidebar</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->

	<!-- Start Blog Single -->
	<section class="blog-single section">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 col-12">
					<div class="blog-single-main">
						<div class="row">
							<div class="col-12">
								<div class="image">
									<img src="https://via.placeholder.com/950x460" alt="#">
								</div>
								<div class="blog-detail">
									<h2 class="blog-title">What are the secrets to start- up success?</h2>
									<div class="blog-meta">
										<span class="author"><a href="#"><i class="fa fa-user"></i>By Admin</a><a href="#"><i class="fa fa-calendar"></i>Dec 24, 2018</a><a href="#"><i class="fa fa-comments"></i>Comment (15)</a></span>
									</div>
									<div class="content">
										<p>What a crazy time. I have five children in colleghigh school graduates.jpge or pursing post graduate studies Each of my children attends college far from home, the closest of which is more than 800 miles away. While I miss being with my older children, I know that a college experience can be the source of great growth and experience can be the source of source of great growth and can provide them with even greater in future.</p>
										<blockquote> <i class="fa fa-quote-left"></i> Do what you love to do and give it your very best. Whether it's business or baseball, or the theater, or any field. If you don't love what you're doing and you can't give it your best, get out of it. Life is too short. You'll be an old man before you know it. risus. Ut tincidunt, erat eget feugiat eleifend, eros magna dapibus diam.</blockquote>
										<p>What a crazy time. I have five children in colleghigh school graduates.jpge or pursing post graduate studies Each of my children attends college far from home, the closest of which is more than 800 miles away. While I miss being with my older children, I know that a college experience can be the source of great growth and experience can be the source of source of great growth and can provide them with even greater in future.</p>
										<p>What a crazy time. I have five children in colleghigh school graduates.jpge or pursing post graduate studies Each of my children attends college far from home, the closest of which is more than 800 miles away. While I miss being with my older children, I know that a college experience can be the source of great growth and experience can be the source of source of great growth and can provide them with even greater in future.</p>
									</div>
								</div>
								<div class="share-social">
									<div class="row">
										<div class="col-12">
											<div class="content-tags">
												<h4>Tags:</h4>
												<ul class="tag-inner">
													<li><a href="#">Glass</a></li>
													<li><a href="#">Pant</a></li>
													<li><a href="#">t-shirt</a></li>
													<li><a href="#">swater</a></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-12">
								<div class="comments">
									<h3 class="comment-title">Comments (3)</h3>
									<!-- Single Comment -->
									<div class="single-comment">
										<img src="https://via.placeholder.com/80x80" alt="#">
										<div class="content">
											<h4>Alisa harm <span>At 8:59 pm On Feb 28, 2018</span></h4>
											<p>Enthusiastically leverage existing premium quality vectors with enterprise-wide innovation collaboration Phosfluorescently leverage others enterprisee Phosfluorescently leverage.</p>
											<div class="button">
												<a href="#" class="btn"><i class="fa fa-reply" aria-hidden="true"></i>Reply</a>
											</div>
										</div>
									</div>
									<!-- End Single Comment -->
									<!-- Single Comment -->
									<div class="single-comment left">
										<img src="https://via.placeholder.com/80x80" alt="#">
										<div class="content">
											<h4>john deo <span>Feb 28, 2018 at 8:59 pm</span></h4>
											<p>Enthusiastically leverage existing premium quality vectors with enterprise-wide innovation collaboration Phosfluorescently leverage others enterprisee Phosfluorescently leverage.</p>
											<div class="button">
												<a href="#" class="btn"><i class="fa fa-reply" aria-hidden="true"></i>Reply</a>
											</div>
										</div>
									</div>
									<!-- End Single Comment -->
									<!-- Single Comment -->
									<div class="single-comment">
										<img src="https://via.placeholder.com/80x80" alt="#">
										<div class="content">
											<h4>megan mart <span>Feb 28, 2018 at 8:59 pm</span></h4>
											<p>Enthusiastically leverage existing premium quality vectors with enterprise-wide innovation collaboration Phosfluorescently leverage others enterprisee Phosfluorescently leverage.</p>
											<div class="button">
												<a href="#" class="btn"><i class="fa fa-reply" aria-hidden="true"></i>Reply</a>
											</div>
										</div>
									</div>
									<!-- End Single Comment -->
								</div>
							</div>
							<div class="col-12">
								<div class="reply">
									<div class="reply-head">
										<h2 class="reply-title">Leave a Comment</h2>
										<!-- Comment Form -->
										<form class="form" action="#">
											<div class="row">
												<div class="col-lg-6 col-md-6 col-12">
													<div class="form-group">
														<label>Your Name<span>*</span></label>
														<input type="text" name="name" placeholder="" required="required">
													</div>
												</div>
												<div class="col-lg-6 col-md-6 col-12">
													<div class="form-group">
														<label>Your Email<span>*</span></label>
														<input type="email" name="email" placeholder="" required="required">
													</div>
												</div>
												<div class="col-12">
													<div class="form-group">
														<label>Your Message<span>*</span></label>
														<textarea name="message" placeholder=""></textarea>
													</div>
												</div>
												<div class="col-12">
													<div class="form-group button">
														<button type="submit" class="btn">Post comment</button>
													</div>
												</div>
											</div>
										</form>
										<!-- End Comment Form -->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-12">
					<div class="main-sidebar">
						<!-- Single Widget -->
						<div class="single-widget search">
							<div class="form">
								<input type="email" placeholder="Search Here...">
								<a class="button" href="#"><i class="fa fa-search"></i></a>
							</div>
						</div>
						<!--/ End Single Widget -->
						<!-- Single Widget -->
						<div class="single-widget category">
							<h3 class="title">Blog Categories</h3>
							<ul class="categor-list">
								<?php foreach ($result_blog_categories as $category): ?>
										
									<li><a href="blog.php"><?= $category['name'] ?></a></li>
									
									</a>
								<?php endforeach; ?>
							</ul>
						</div>
						<!--/ End Single Widget -->
						<!-- Single Widget -->
						<div class="single-widget recent-post">
							<h3 class="title">Recent post</h3>
							<!-- Single Post -->
							<div class="single-post">
								<div class="image">
									<img src="https://via.placeholder.com/100x100" alt="#">
								</div>
								<div class="content">
									<h5><a href="#">Top 10 Beautyful Women Dress in the world</a></h5>
									<ul class="comment">
										<li><i class="fa fa-calendar" aria-hidden="true"></i>Jan 11, 2020</li>
										<li><i class="fa fa-commenting-o" aria-hidden="true"></i>35</li>
									</ul>
								</div>
							</div>
							<!-- End Single Post -->
							<!-- Single Post -->
							<div class="single-post">
								<div class="image">
									<img src="https://via.placeholder.com/100x100" alt="#">
								</div>
								<div class="content">
									<h5><a href="#">Top 10 Beautyful Women Dress in the world</a></h5>
									<ul class="comment">
										<li><i class="fa fa-calendar" aria-hidden="true"></i>Mar 05, 2019</li>
										<li><i class="fa fa-commenting-o" aria-hidden="true"></i>59</li>
									</ul>
								</div>
							</div>
							<!-- End Single Post -->
							<!-- Single Post -->
							<div class="single-post">
								<div class="image">
									<img src="https://via.placeholder.com/100x100" alt="#">
								</div>
								<div class="content">
									<h5><a href="#">Top 10 Beautyful Women Dress in the world</a></h5>
									<ul class="comment">
										<li><i class="fa fa-calendar" aria-hidden="true"></i>June 09, 2019</li>
										<li><i class="fa fa-commenting-o" aria-hidden="true"></i>44</li>
									</ul>
								</div>
							</div>
							<!-- End Single Post -->
						</div>
						<!--/ End Single Widget -->
						<!-- Single Widget -->
						<!--/ End Single Widget -->
						<!-- Single Widget -->
						<div class="single-widget side-tags">
							<h3 class="title">Tags</h3>
							<ul class="tag">
								<li><a href="#">business</a></li>
								<li><a href="#">wordpress</a></li>
								<li><a href="#">html</a></li>
								<li><a href="#">multipurpose</a></li>
								<li><a href="#">education</a></li>
								<li><a href="#">template</a></li>
								<li><a href="#">Ecommerce</a></li>
							</ul>
						</div>
						<!--/ End Single Widget -->
						<!-- Single Widget -->
						<div class="single-widget newsletter">
							<h3 class="title">Newslatter</h3>
							<div class="letter-inner">
								<h4>Subscribe & get news <br> latest updates.</h4>
								<div class="form-inner">
									<input type="email" placeholder="Enter your email">
									<a href="#">Submit</a>
								</div>
							</div>
						</div>
						<!--/ End Single Widget -->
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--/ End Blog Single -->

	<!-- Start Footer Area -->
	<?php include('components/footer.php') ?>
	<!-- /End Footer Area -->

	<!-- Jquery -->
	<?php include('components/script.php') ?>
</body>

</html>