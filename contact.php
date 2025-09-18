<?php 
session_start();

require_once 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="eng">
<!-- head goes here -->
 <?php include('components/head.php');?>
<body class="js">
	
	

		
		<!-- Header -->
	<?php include('components/header.php');?>
		<!--/ End Header -->
	
	<!-- Breadcrumbs -->
	<div class="breadcrumbs">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="bread-inner">
						<ul class="bread-list">
							<li><a href="index.php">Home<i class="ti-arrow-right"></i></a></li>
							<li class="active">Contact</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->
  
	<!-- Start Contact -->
	<section id="contact-us" class="contact-us section">
		<div class="container">
				<div class="contact-head">
					<div class="row">
						<div class="col-lg-8 col-12">
							<div class="form-main">
								<div class="title">
									<h4>Get in touch</h4>
									<h3>Write us a message</h3><small>Fields marked in red are madatory</small>
								</div>
								<form class="form" method="post" action="mail/mail.php">
									<div class="row">
										<div class="col-lg-6 col-12">
											<div class="form-group">
												<input name="name" type="text" placeholder="Name" class="form-control rounded" style="border-color: red;" required>
											</div>
										</div>
										<div class="col-lg-6 col-12">
											<div class="form-group">
												<input name="subject" type="text" placeholder="Subject" class="form-control rounded" style="border-color: red;" required>
											</div>
										</div>
										<div class="col-lg-6 col-12">
											<div class="form-group">
												<input name="email" type="email" placeholder="Email" class="form-control rounded" style="border-color: red;" required>
											</div>	
										</div>
										<div class="col-lg-6 col-12">
											<div class="form-group">
												<input name="mobile" type="text" placeholder="Mobile" class="form-control rounded">
											</div>	
										</div>
										<div class="col-12">
											<div class="form-group message">
												<textarea name="message" placeholder="Message" class="form-control rounded"  style="border-color: red;"required></textarea>
											</div>
										</div>
										<div class="col-12">
											<div class="form-group button">
												<button type="submit" class="btn ">Send Message</button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
						<div class="col-lg-4 col-12">
							<div class="single-head">
								<div class="single-info">
									<i class="fa fa-phone"></i>
									<h4 class="title">Call us Now:</h4>
									<ul>
										<li>+234-813-772-6887</li>
										<li>+234-704-324-7461</li>
									</ul>
								</div>
								<div class="single-info">
									<i class="fa fa-envelope-open"></i>
									<h4 class="title">Email:</h4>
									<ul>
										<li><a href="mailto:info@yourwebsite.com">enquiries@dinolabstech.com</a></li>
										<li><a href="mailto:info@yourwebsite.com">admin@dinolabstech.com</a></li>
									</ul>
								</div>
								<div class="single-info">
									<i class="fa fa-location-arrow"></i>
									<h4 class="title">Our Address:</h4>
									<ul>
										<li>Suite 5 Wing-B, TISCO building Alagbaka Akure, Ondo state Nigeria. </li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	</section>
	<!--/ End Contact -->
	
	<!-- Map Section -->
	<div class="map-section">
		<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d640.6690182300736!2d5.2145191194978935!3d7.252504593673251!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sng!4v1745751141530!5m2!1sen!2sng" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
	</div>
	<!--/ End Map Section -->
	

	<!-- Start Footer Area -->
<?php include('components/footer.php');?>
	<!-- /End Footer Area -->
	
	
 <!-- scripts goes here -->
  <?php include('components/script.php');?>
</body>
</html>