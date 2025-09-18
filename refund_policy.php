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
                            <li class="active">Refund Policy</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

     <div class="container pt-5">
        <h1>Dinolabs Tech Services Refund Policy</h1>
        <p>Dinolabs Tech Services will process all refund requests in line with this Refund Policy.</p>
        <p>Note that Dinolabs Tech Services may vary this Refund Policy at any time. If we vary this Refund Policy, we will provide notice by publishing the varied Refund Policy on our Website. You accept that by doing this, Dinolabs Tech Services has provided you with sufficient notice of the variation to its Refund Policy. Your continued use of our Website Software services will be deemed as acceptance of the varied terms by you.</p>
        <p>To request a refund, you must submit a service cancellation request through by sending an email to enquiries@dinolabs.org within the time stipulated in this Refund Policy for the relevant service you wish to cancel.</p>

        <h2 class="pt-3">General Refund Process</h2>
        <ul>
            <li>Service(s) must be cancelled before a refund can be issued</li>
            <li>You will not be entitled to a refund if your domain name has been flagged as suspicious, is considered to be registered for improper use, or is registered in breach of our Terms of Service.</li>
            <li>You will not be entitled to a refund if your service is suspended or terminated as a result of a breach of our Terms of Service.</li>
            <li>You will not be entitled to a refund if your license has been generated or downloaded after purchase.</li>
            <li>All eligible refunds will be automatically credited to your client account with Dinolabs Tech Services, unless you specifically request for a cash refund. Money refunded into your client account can be used at a later date to pay for other products and services. You can view your credit balance by logging into your client account and going to the section on your client area dashboard.</li>
            <li>If you have requested a cash refund, Dinolabs Tech Services will only give such a refund where the account details you have provided for the refund are an exact match with that from which we received your payment. In event of any discrepancy in the account details, an eligible refund will only be made into your client account.</li>
            <li>Requests for cash refunds will be processed and completed within a minimum of 5 days and a maximum of 15 days from the date of request. All cash refund requests are subject to an administrative fee which will be deducted from the amount to be refunded to you. In the event that the amount to be refunded is less than the administrative fee i.e bank charges, you will only be entitled to a refund into your client account.</li>
            <li>On no account will the same product or service be entitled to a refund more than once.</li>
            <li>No portion of your Wallet Balance may be transferred to another Dinolabs Tech Services account.</li>
            <li>This Refund Policy for Dinolabs Tech Services Wallet may be amended from time to time.</li>
        </ul>

        <h2 class="pt-3">Overpayment</h2>
        <p>If we become aware that you have overpaid for any product or service, we will automatically credit the amount of that overpayment to your Dinolabs Tech Services account where you can use it to pay for other products or services at a later date. You will be able to see this on your client area dashboard.</p>
        <p>If you wish for an overpayment to be refunded to your bank account, you must send a request for a refund to enquiries@dinolabs.org The request must give required details including the license code generated for which the overpayment was made, date of payment, method of payment (including, where applicable, bank details from which payment was made) and amount of overpayment.</p>
        <p>Once we have received your request, it will be dealt with in accordance with this Refund Policy.</p>

        <p>This Refund Policy was last modified on [21st March 2025]</p>
</div>

<!-- Start Footer Area -->
    <?php include('components/footer.php') ?>
    <!-- /End Footer Area -->

    <!-- Jquery -->
    <?php include('components/script.php') ?>
</body>
</html>