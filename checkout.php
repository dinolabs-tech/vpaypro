<?php

require_once 'db_connect.php';
require_once 'cart_functions.php';

if (!isset($_SESSION['staffname'])) {
	header("Location: login.php");
	exit;
}

// Fetch user details
$staffname = $_SESSION['staffname'];
$user_id = $_SESSION['user_id']; // Ensure user_id is available
$user_details = [];
$stmt = $conn->prepare("SELECT first_name, last_name, email, address, contact, country, state FROM users WHERE username = ?");
$stmt->bind_param('s', $user_name);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
	$user_details = $result->fetch_assoc();
}
$stmt->close();

// Fetch cart items from database and calculate total
$total_amount = 0;
$cart_items_db = [];
if (!empty($user_id)) {
    $sql_cart = "SELECT products.price, cart.quantity FROM products JOIN cart ON products.id = cart.product_id WHERE cart.user_id = ?";
    $stmt_cart = $conn->prepare($sql_cart);
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $result_cart = $stmt_cart->get_result();
    while ($item = $result_cart->fetch_assoc()) {
        $total_amount += $item['price'] * $item['quantity'];
    }
    $stmt_cart->close();
}

// Fetch Flutterwave Public Key from payment_gateways table
$flutterwave_public_key = '';
$stmt = $conn->prepare("SELECT flutterwave_public_key FROM payment_gateways WHERE gateway_name = 'flutterwave' AND is_active = 1 LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $flutterwave_public_key = $row['flutterwave_public_key'];
}
$stmt->close();

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
							<li class="active">Checkout</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->

	<!-- Start Checkout -->
	<section class="shop checkout section">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-12">
					<div class="checkout-form">
						<h2>Make Your Checkout Here</h2>
						<p>Please register in order to checkout more quickly</p>
						<!-- Form -->
						<form class="form" id="checkoutForm" method="post" action="#">
							<div class="row">
								<div class="col-lg-6 col-md-6 col-12">
									<div class="form-group">
										<label>Full Name<span>*</span></label>
										<input readonly type="text" name="name" placeholder="" required="required" value="<?php echo htmlspecialchars(($user_details['first_name'] ?? '') . ' ' . ($user_details['last_name'] ?? '')); ?>">
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-12">
									<div class="form-group">
										<label>Email Address<span>*</span></label>
										<input readonly type="email" name="email" placeholder="" required="required" value="<?php echo htmlspecialchars($user_details['email'] ?? ''); ?>">
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-12">
									<div class="form-group">
										<label>Phone Number<span>*</span></label>
										<input readonly type="number" name="number" placeholder="" required="required" value="<?php echo htmlspecialchars($user_details['contact'] ?? ''); ?>">
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-12">
									<div class="form-group">
										<label>State<span>*</span></label>
										<input name="state" placeholder="" required="required" value="<?php echo htmlspecialchars($user_details['state'] ?? ''); ?>">
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-12">
									<div class="form-group">
										<label>Country<span>*</span></label>
										<input type="text" name="country" placeholder="" required="required" value="<?php echo htmlspecialchars($user_details['country'] ?? ''); ?>">
									</div>
								</div>
								<div class="col-lg-12 col-12">
									<div class="form-group">
										<label>Shipping Address<span>*</span></label>
										<input type="text" name="address" placeholder="" required="required" value="<?php echo htmlspecialchars($user_details['address'] ?? ''); ?>">
									</div>
								</div>

								<div class="col-lg-4 col-4">
									<div class="form-group">
										<div class="single-widget get-button">
											<div class="content">
												<div class="button">
													<button type="button" class="btn rounded text-white" onclick="makePayment()" style="width:300px;">Proceed to Checkout</button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</form>
						<!--/ End Form -->
					</div>
				</div>
			</div>
		</div>

	</section>
	<!--/ End Checkout -->

	<!-- Start Shop Services Area  -->
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
	<!-- End Shop Services -->

	<!-- Start Shop Newsletter  -->

	<!-- End Shop Newsletter -->

	<!-- Start Footer Area -->
	<?php include('components/footer.php') ?>
	<!-- /End Footer Area -->

	<!-- Jquery -->
	<?php include('components/script.php') ?>
	<script src="https://checkout.flutterwave.com/v3.js"></script>
	<script>
		function makePayment() {
			FlutterwaveCheckout({
				public_key: "<?php echo htmlspecialchars($flutterwave_public_key); ?>",
				tx_ref: "BUYVERSE_<?php echo time(); ?>_<?php echo mt_rand(1000, 9999); ?>",
				amount: <?php echo $total_amount; ?>,
				currency: "NGN", // Assuming NGN, make configurable if needed
				country: "<?php echo htmlspecialchars($user_details['country'] ?? 'NG'); ?>", // Default to NG if not set
				customer: {
					email: "<?php echo htmlspecialchars($user_details['email'] ?? ''); ?>",
					phone_number: "<?php echo htmlspecialchars($user_details['contact'] ?? ''); ?>",
					name: "<?php echo htmlspecialchars(($user_details['first_name'] ?? '') . ' ' . ($user_details['last_name'] ?? '')); ?>",
				},
				customizations: {
					title: "BuyVerse Payment",
					description: "Payment for items from BuyVerse",
					logo: "http://localhost/buyverse/images/logo.png", // Replace with actual logo URL
				},
				callback: function(data) {
					// Redirect to your callback page for server-side verification
					window.location.href = 'flutterwave_callback.php?status=' + data.status + '&tx_ref=' + data.tx_ref + '&transaction_id=' + data.transaction_id;
				},
				onclose: function() {
					// User closed the modal, optionally redirect or show a message
				},
			});
		}
	</script>
</body>

</html>
