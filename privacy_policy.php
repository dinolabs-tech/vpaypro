<?php
// Start a new session or resume the existing session.
session_start();

// Include the database connection file. This file is responsible for establishing a connection to the database.
include("db_connect.php");
?>

<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php') ?>

<body class="js">
     <?php include('components/header.php') ?>

     <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="index.php">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active">Privacy Policy</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

   <div class="container pt-5">
        <!-- Main Heading for the Privacy Policy Document -->
        <h1>Dinolabs Tech Services Privacy Policy</h1>
        <p>This policy covers how we use your personal information. We take your privacy seriously and will take all measures to protect your personal information.</p>
        <p>We are committed to protecting your privacy</p>
        <p>We collect the minimum amount of information about you that is commensurate with providing you with a satisfactory service. The purpose of this Privacy Policy to enable you to understand which personal identifying information ("PII", "Personal Information") of yours is collected, how and when we might use your information, who has access to this information, and how you can correct any inaccuracies in the information. To better protect your privacy, this policy explains our online information practices and the choices you can make about the way your information is collected and used. For easy access, we have made this policy available on our website.</p>

        <!-- Section: Information Collected -->
        <h2 class="pt-3">Information Collected</h2>
        <p>We may collect any or all of the information you provide us via automated means such as communications profiles. The personal information you give us may include your name, address, telephone number, and email address, dates of service provided, types of service provided, payment history, manner of payment, amount of payments, date of payments or other payment information. The financial information will only be used to bill you for the products and services you purchased. If you purchase by credit card, this information is forwarded to your credit card provider. All sensitive information is collected on a secure server and data is transferred.</p>

        <!-- Section: Information Use -->
        <h2 class="pt-3">Information Use</h2>
        <p>This information is used for billing and to provide service and support to our customers. We may also study this information to determine our customers' needs and provide support for our customers. All reasonable precautions are taken to prevent unauthorized access to this information. Our precautionary measures may require you to provide additional forms of identity should you wish to obtain information about your account details.</p>

        <!-- Section: What Constitutes your Consent? -->
        <h2 class="pt-3">What Constitutes your Consent?</h2>
        <p>Where the processing of Personal Data is based on consent, we shall obtain the requisite consent at the time of collection of the Personal Data. In this regard, you consent to the processing of your Personal Data when you access our platforms or use our services, content, features, technologies, or functions offered on our website or other digital platforms. You can withdraw your consent at any time but such withdrawal will not affect the lawfulness of the processing of your data based on consent given before its withdrawal.</p>

        <!-- Section: Disclosing Information -->
        <h2 class="pt-3">Disclosing Information</h2>
        <p>We do not disclose any personal information obtained about you from this website to third parties.</p>
        <p>We may use personal information to keep in contact with you and inform you of developments associated with our business. We may also disclose aggregate, anonymous data based on information collected from users to potential partners, our affiliates, and reputable third parties. We take all available measures to select affiliates and service providers that are ethical and provide similar privacy protection to their customers and the community. We do not make any representations about the practices and policies of these companies.</p>

        <!-- Section: Security and Retention of your Personal Data -->
        <h2 class="pt-3">Security and Retention of your Personal Data</h2>
        <p>Your Personal Data is kept private and We make every effort to keep your Personal Data secure, including restricting access to your Personal Data with us on a need to know basis. We require our staff and any third parties who carry out any work on our behalf to comply with appropriate security standards to protect your Personal Data.</p>
        <p>We take appropriate measures to ensure that your Personal Data is only processed for the minimum period necessary in line with the purposes set out in this policy notice or as required by applicable laws until a time it is no longer required or has no use. Once your Personal Data is no longer required, we destroy it in a safe and secure manner.</p>

        <!-- Section: Compliance with Laws and Law Enforcement -->
        <h2 class="pt-3">Compliance with Laws and Law Enforcement</h2>
        <p>We cooperate with government and law enforcement officials to enforce and comply with the law. We will disclose any information about Users upon valid request by government or law officials as we, in our sole discretion, believe necessary or appropriate to respond to claims and legal process (including without limitation subpoenas), to protect your property and rights, or the property and rights of a third party, to protect the safety of the public or any person, or stop activity we consider illegal or unethical.</p>

        <!-- Section: Changes to this Policy -->
        <h2 class="pt-3">Changes to this Policy</h2>
        <p>Any changes to our Privacy Policy will be placed here and will supersede this version of our Policy. We will take reasonable steps to draw your attention to any changes in our Policy. However, to be on the safe side, we suggest that you read this document each time you use the website to ensure that it still meets with your approval.</p>
</div>

<!-- Start Footer Area -->
    <?php include('components/footer.php') ?>
    <!-- /End Footer Area -->

    <!-- Jquery -->
    <?php include('components/script.php') ?>
</body>
</html>