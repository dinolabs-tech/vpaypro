<?php
if (empty($_SESSION['user_id'])) {
} else {
	$user_id = $_SESSION['user_id'];
}

include('db_connect.php');



// Fetch categories
$sql_categories = "SELECT * FROM categories";
$result_categories = $conn->query($sql_categories);

?>

<header class="header shop">
	<!-- Topbar -->
	<div class="topbar">
		<div class="container">
			<div class="row">
				<div class="col-lg-4 col-md-12 col-12">
					<!-- Top Left -->
					<div class="top-left">
						<ul class="list-main" style="width: 400px;">
							<li><i class="ti-headphone-alt"></i> +234 704 324 7461</li>
							<li><i class="ti-email"></i> enquiries@dinolabstech.com</li>
						</ul>
					</div>
					<!--/ End Top Left -->
				</div>
				<div class="col-lg-8 col-md-12 col-12">
					<!-- Top Right -->
					<div class="right-content">
						<ul class="list-main">
							<!-- <li><i class="ti-location-pin"></i> Suite 5, TISCO Building Alagbaka Akure.</li> -->
							<?php
							if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 0)) { ?>
								<li><i class="ti-settings"></i> <a href="admin_payment_settings.php">Settings</a></li>
							<?php } ?>

							<?php if (isset($_SESSION['user_id'])) { ?>
								<?php if ($_SESSION['role'] == 'Customer') { ?>
									<li><i class="ti-user"></i> <a href="backend/customer_profile.php">My account</a></li>
									<li><a href="logout.php"><i class="ti-power-off"></i>Logout</a></li>
								<?php  } elseif ($_SESSION['role'] == 'Supplier') { ?>
									<li><i class="ti-user"></i> <a href="backend/supplier_profile.php">My account</a></li>
									<li><a href="logout.php"><i class="ti-power-off"></i>Logout</a></li>
								<?php  } else { ?>
									<li><i class="ti-user"></i> <a href="backend/profile.php">My account</a></li>
									<li><a href="logout.php"><i class="ti-power-off"></i>Logout</a></li>
								<?php } ?>

							<?php } else { ?>
								<li><a href="backend/index.php"><i class="ti-power-off"></i>Login</a></li>
								<li><a href="backend/customer_register.php"><i class="ti-power-off"></i>Register</a></li>
							<?php } ?>

						</ul>
					</div>
					<!-- End Top Right -->
				</div>
			</div>
		</div>
	</div>
	<!-- End Topbar -->
	<div class="middle-inner">
		<div class="container">
			<div class="row">
				<div class="col-lg-2 col-md-2 col-12">
					<!-- Logo -->
					<div class="logo">
						<a href="index.php"><img src="images/logo.png" alt="logo"></a>
					</div>
					<!--/ End Logo -->
					<!-- Search Form -->
					<div class="search-top">
						<div class="top-search"><a href="#0"><i class="ti-search"></i></a></div>
						<!-- Mobile 4Search Form -->
						<div class="search-top">
							<form class="search-form" action="search_results.php" method="GET">
								<!-- <input type="hidden" name="page" value="search_results"> -->
								<input name="search_query" placeholder="Search Products Here....." type="text">
								<button type="submit" name="search"><i class="ti-search"></i></button>
							</form>
						</div>
						<!--/ End Search Form -->
					</div>
					<!--/ End Search Form -->
					<div class="mobile-nav"></div>
				</div>
				<div class="col-lg-8 col-md-6 col-12">
					<div class="search-bar-top">
						<div class="search-bar">

							<form action="search_results.php" method="GET">
								<!-- <input type="hidden" name="page" value="search_results"> -->
								<input name="search_query" placeholder="Search Products Here....." type="text">
								<button type="submit" class="btnn" name="search"><i class="ti-search"></i></button>
							</form>
						</div>

					</div>
				</div>
				<div class="col-lg-2 col-md-3 col-12">
					<div class="right-bar">
						<?php if (!empty($_SESSION['user_id'])) { ?>
							<div class="sinlge-bar">
								<a href="wishlist.php" class="single-icon"><i class="fa fa-heart-o" aria-hidden="true"></i></a>
							</div>
							<div class="sinlge-bar">
								<a href="compare.php" class="single-icon"><i class="ti-bar-chart-alt" aria-hidden="true"></i></a>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<!-- Header Inner -->
		<div class="header-inner">
			<div class="container-fluid">
				<div class="cat-nav-head">
					<div class="row justify-content-center">

						<div class="col-lg-9 col-12">
							<div class="menu-area">
								<!-- Main Menu -->
								<nav class="navbar navbar-expand-lg">
									<div class="navbar-collapse">
										<div class="nav-inner">
											<ul class="nav main-menu menu navbar-nav">


												<li><a href="index.php">Home</i></a></li>
												<!-- <li><a href="hot_deals.php">Discounted Deals</a></li> -->
												<li><a href="new_product.php"><span class="new">New</span>New Products</a></li>


												<?php if (isset($_SESSION['user_id'])) { ?>
													<?php if ($_SESSION['role'] == 'Superuser' || $_SESSION['role'] == 'CEO') { ?>
														<li><a href="#">Products<i class="ti-angle-down"></i></a>
															<ul class="dropdown">
																<li><a href="products.php">Products</a></li>
																<li><a href="backend/inventory.php">Manage Products</a></li>
																<li><a href="backend/admin_categories.php">Manage Categories</a></li>
															</ul>
														</li>

														<li><a href="#">Shopping<i class="ti-angle-down"></i><span class="new bg-danger">Hot</span></a>
															<ul class="dropdown">
																<!-- <li><a href="cart.php">Cart</a></li> -->
																<li><a href="wishlist.php">Wishlist</a></li>
																<li><a href="compare.php">Compare</a></li>

															</ul>
														</li>

														<li><a href="backend/admin_orders.php">Orders</a></li>
														<li><a href="backend/users.php">Users</a></li>
														<!-- <li><a href="checkout.php">Checkout</a></li> -->
														<li><a href="#">Blog<i class="ti-angle-down"></i></a>
															<ul class="dropdown">
																<li><a href="blog.php">Posts</a></li>
																<li><a href="add_blog_post.php">Create New Post</a></li>
																<li><a href="blog_categories.php">Manage Categories</a></li>
																<li><a href="blog_dashboard.php">Dashboard</a></li>
															</ul>
														</li>
														<li><a href="backend/dashboard.php">Dashboard</a></li>
														<li><a href="contact.php">Contact</a></li>

													<?php } elseif ($_SESSION['role'] == 'Administrator') { ?>
														<li><a href="#">Products<i class="ti-angle-down"></i></a>
															<ul class="dropdown">
																<li><a href="products.php">Products</a></li>
																<li><a href="backend/inventory.php">Manage Products</a></li>
																<li><a href="backend/admin_categories.php">Manage Categories</a></li>
															</ul>
														</li>

														<li><a href="#">Shopping<i class="ti-angle-down"></i><span class="new bg-danger">Hot</span></a>
															<ul class="dropdown">
																<!-- <li><a href="cart.php">Cart</a></li> -->
																<li><a href="wishlist.php">Wishlist</a></li>
																<li><a href="compare.php">Compare</a></li>

															</ul>
														</li>

														<li><a href="backend/admin_orders.php">Orders</a></li>
														<!-- <li><a href="backend/users.php">Users</a></li> -->
														<!-- <li><a href="checkout.php">Checkout</a></li> -->
														<li><a href="#">Blog<i class="ti-angle-down"></i></a>
															<ul class="dropdown">
																<li><a href="blog.php">Posts</a></li>
																<li><a href="add_blog_post.php">Create New Post</a></li>
																<li><a href="blog_categories.php">Manage Categories</a></li>
																<li><a href="blog_dashboard.php">Dashboard</a></li>
															</ul>
														</li>
														<li><a href="backend/dashboard.php">Dashboard</a></li>
														<li><a href="contact.php">Contact</a></li>

													<?php } elseif ($_SESSION['role'] == 'Inventory Manager') { ?>
														<li><a href="#">Products<i class="ti-angle-down"></i></a>
															<ul class="dropdown">
																<li><a href="products.php">Products</a></li>
																<li><a href="backend/inventory.php">Manage Products</a></li>
																<li><a href="backend/admin_categories.php">Manage Categories</a></li>
															</ul>
														</li>
														<li><a href="#">Shopping<i class="ti-angle-down"></i><span class="new bg-danger">Hot</span></a>
															<ul class="dropdown">
																<!-- <li><a href="cart.php">Cart</a></li> -->
																<li><a href="wishlist.php">Wishlist</a></li>
																<li><a href="compare.php">Compare</a></li>
															</ul>
														</li>
														<li><a href="backend/admin_orders.php">Orders</a></li>
														<li><a href="blog.php">Blog</a></li>
														<li><a href="backend/inventory.php">Dashboard</a></li>
														<li><a href="contact.php">Contact</a></li>

													<?php } elseif ($_SESSION['role'] == 'Sales Manager') { ?>
														<li><a href="products.php">Products</a></li>
														<li><a href="#">Shopping<i class="ti-angle-down"></i><span class="new bg-danger">Hot</span></a>
															<ul class="dropdown">
																<!-- <li><a href="cart.php">Cart</a></li> -->
																<li><a href="wishlist.php">Wishlist</a></li>
																<li><a href="compare.php">Compare</a></li>
															</ul>
														</li>
														<li><a href="backend/admin_orders.php">Orders</a></li>
														<li><a href="blog.php">Blog</a></li>
														<li><a href="backend/pos.php">Dashboard</a></li>
														<li><a href="contact.php">Contact</a></li>

													<?php } elseif ($_SESSION['role'] == 'Cashier') { ?>
														<li><a href="products.php">Products</a></li>
														<li><a href="#">Shopping<i class="ti-angle-down"></i><span class="new bg-danger">Hot</span></a>
															<ul class="dropdown">
																<!-- <li><a href="cart.php">Cart</a></li> -->
																<li><a href="wishlist.php">Wishlist</a></li>
																<li><a href="compare.php">Compare</a></li>
															</ul>
														</li>
														<li><a href="backend/admin_orders.php">Orders</a></li>
														<li><a href="blog.php">Blog</a></li>
														<li><a href="backend/pos.php">Dashboard</a></li>
														<li><a href="contact.php">Contact</a></li>

													<?php } elseif ($_SESSION['role'] == 'Delivery') { ?>
														<li><a href="products.php">Products</a></li>
														<li><a href="blog.php">Blog</a></li>
														<li><a href="backend/admin_orders.php">Dashboard</a></li>
														<li><a href="contact.php">Contact</a></li>

														<?php } elseif ($_SESSION['role'] == 'Supplier') { ?>
														<li><a href="products.php">Products</a></li>
														<li><a href="blog.php">Blog</a></li>
														<li><a href="backend/supplier_dashboard.php">Dashboard</a></li>
														<li><a href="contact.php">Contact</a></li>

													<?php } elseif ($_SESSION['role'] == 'Customer') { ?>
														<li><a href="products.php">Products</a></li>
														<li><a href="#">Shopping<i class="ti-angle-down"></i><span class="new bg-danger">Hot</span></a>
															<ul class="dropdown">
																<li><a href="backend/online_store.php">Cart</a></li>
																<li><a href="wishlist.php">Wishlist</a></li>
																<li><a href="compare.php">Compare</a></li>
															</ul>
														</li>
														<li><a href="backend/customer_orders.php">Orders</a></li>
														<li><a href="backend/online_store.php">Checkout</a></li>
														<li><a href="blog.php">Blog</a></li>
														<li><a href="backend/customer_dashboard.php">Dashboard</a></li>
														<li><a href="contact.php">Contact</a></li>

													<?php } ?>

												<?php } else { ?>

													<li><a href="products.php">Products</a></li>
													<li><a href="blog.php">Blog</a></li>
													<li><a href="contact.php">Contact</a></li>
												<?php } ?>

												<!-- <li><a href="mail-success.php">Mail Success</a></li>
													<li><a href="404.php">404</a></li> -->


											</ul>
										</div>
									</div>
								</nav>
								<!--/ End Main Menu -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--/ End Header Inner -->
</header>