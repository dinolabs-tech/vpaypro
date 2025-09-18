<?php

include('db_connect.php');
include('includes/functions.php');

?>

<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php')?>
<body class="js">
	
	
	
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
							<li class="active"><a href="blog-single.php">About Us</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->
	
	<!-- About Us -->
	<section class="about-us section">
			<div class="container">
				 <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="section-title text-center position-relative pb-3 mb-5 mx-auto" style="max-width: 6000px;">
                <!-- Display the section title, emphasizing innovation and progress. -->
                <h5 class="fw-bold text-primary text-uppercase">Innovating Solutions. Empowering Progress.</h5>
                <!-- The commented out line below suggests a previous or alternative heading. -->
                <!-- <h1 class="mb-0">We are Offering Competitive Prices for Our Clients</h1> -->
            </div>
            <div class="row g-0">
                <!-- Our Mission Column -->
                <!-- This column details the company's core mission statement. -->
                <div class="col-lg-4 wow slideInUp" data-wow-delay="0.6s">
                    <div class="bg-light rounded">
                        <div class="border-bottom py-4 px-5 mb-4">
                            <!-- Heading for the mission statement. -->
                            <h4 class="text-primary mb-1">Our Mission</h4>
                        </div>
                        <div class="p-5 pt-0">
                            <!-- The actual mission statement text. -->
                            <p>
                                To build and deliver smart, efficient, and user-friendly technology solutions that solve real-world problems and empower our clients to thrive in the digital age.
                            </p>
                        </div>
                    </div>
                </div>
                <!-- What We Offer Column -->
                <!-- This central column lists the various services and solutions provided by Dinolabs. -->
                <div class="col-lg-4 wow slideInUp" data-wow-delay="0.3s">
                    <div class="bg-white rounded shadow position-relative" style="z-index: 1;">
                        <div class="border-bottom py-4 px-5 mb-4">
                            <!-- Heading for the services offered. -->
                            <h4 class="text-primary mb-1">What We Offer</h4>
                        </div>
                        <div class="p-5 pt-0">
                            <!-- List of services, each with a check icon. -->
                            <div class="d-flex justify-content-between mb-3"><span>Custom Software Development</span><i
                                    class="fa fa-check text-primary pt-1"></i></div>
                            <div class="d-flex justify-content-between mb-3"><span>Educationl Management
                                    Systems</span><i class="fa fa-check text-primary pt-1"></i></div>
                            <div class="d-flex justify-content-between mb-3"><span>Sales Management Solutions</span><i
                                    class="fa fa-check text-primary pt-1"></i></div>
                            <div class="d-flex justify-content-between mb-3"><span>Pharmacy Solutions</span><i
                                    class="fa fa-check text-primary pt-1"></i></div>
                            <div class="d-flex justify-content-between mb-2"><span>Web Design & Development</span><i
                                    class="fa fa-check text-primary pt-1"></i></div>
                            <div class="d-flex justify-content-between mb-2"><span>Database Management</span><i
                                    class="fa fa-check text-primary pt-1"></i></div>
                            <div class="d-flex justify-content-between mb-2"><span>Technology Consulting</span><i
                                    class="fa fa-check text-primary pt-1"></i></div>
                            <div class="d-flex justify-content-between mb-2"><span>IT Training and Support</span><i
                                    class="fa fa-check text-primary pt-1"></i></div>
                        </div>
                    </div>
                </div>
                <!-- Why Choose Dinolabs? Column -->
                <!-- This column outlines the competitive advantages and reasons for choosing Dinolabs. -->
                <div class="col-lg-4 wow slideInUp" data-wow-delay="0.9s">
                    <div class="bg-light rounded">
                        <div class="border-bottom py-4 px-5 mb-4">
                            <!-- Heading for the reasons to choose Dinolabs. -->
                            <h4 class="text-primary mb-1">Why Choose Dinolabs?</h4>
                        </div>
                        <div class="p-5 pt-0">
                            <!-- Explanation of why Dinolabs is a preferred partner. -->
                            <p>
                                We combine deep industry knowledge with advanced technical expertise to create impactful
                                solutions. Our commitment to excellence, affordable pricing, and strong customer support
                                make us a trusted partner for digital transformation.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mission, Offerings, and Why Choose Us Section End -->


    <!-- Who We Are Section Start -->
    <!-- This section provides a more detailed narrative about the company, its values, and key attributes.
         It includes descriptive text, a list of features, and an accompanying image. -->
    <div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-7">
                    <div class="section-title position-relative pb-3 mb-5">
                        <!-- The commented out line below suggests a previous or alternative subheading. -->
                        <!-- <h5 class="fw-bold text-primary text-uppercase">About Us</h5> -->
                        <!-- Main heading for the "Who We Are" section. -->
                        <h1 class="mb-0">Who We Are</h1>
                    </div>
                    <!-- Detailed description of Dinolabs Tech Services. -->
                    <p class="mb-4">
                        Dinolabs Tech Services is a forward-thinking technology company committed to delivering
                        innovative software solutions that drive digital transformation across various sectors.
                        With a passion for quality and a user-centric approach, we help businesses, schools, and
                        organizations achieve their goals through reliable and scalable technologies.
                    </p>
                    <div class="row g-0 mb-3">
                        <div class="col-sm-6 wow zoomIn" data-wow-delay="0.2s">
                            <!-- Feature item: Award Winning. -->
                            <h5 class="mb-3"><i class="fa fa-check text-primary me-3"></i>Award Winning</h5>
                            <!-- Feature item: Professional Staff. -->
                            <h5 class="mb-3"><i class="fa fa-check text-primary me-3"></i>Professional Staff</h5>
                        </div>
                        <div class="col-sm-6 wow zoomIn" data-wow-delay="0.4s">
                            <!-- Feature item: 24/7 Support. -->
                            <h5 class="mb-3"><i class="fa fa-check text-primary me-3"></i>24/7 Support</h5>
                            <!-- Feature item: Fair Prices. -->
                            <h5 class="mb-3"><i class="fa fa-check text-primary me-3"></i>Fair Prices</h5>
                        </div>
                    </div>
                </div>
                <!-- Image display for the "Who We Are" section, providing a visual context. -->
                <div class="col-lg-5" style="min-height: 500px;">
                    <div class="position-relative h-100">
                        <!-- Image for the about us section, animated to zoom in. -->
                        <img class="position-absolute w-100 h-100 rounded wow zoomIn" data-wow-delay="0.9s"
                            src="assets/images/Women_in_tech_office_(1).jpg" style="object-fit: cover;">
                    </div>
                </div>
            </div>
        </div>
    </div>
			</div>
	</section>
	<!-- End About Us -->
	

	<!-- Start Footer Area -->
<?php include('components/footer.php')?>
	<!-- /End Footer Area -->
 
	<!-- Jquery -->
<?php include('components/script.php')?>
</body>
</html>