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
                            <li class="active">Terms and Conditions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

     <div class="container pt-5">
        <!-- Main content area for the Terms of Service. -->
        <h2>DINOLABS TECH SERVICES TERMS OF SERVICE</h2>
        <!-- Introduction to the Terms of Service, defining the parties involved. -->
        <p>This Website Terms of Service (“the Terms of Service”) is entered into by and between Dinolabs Tech Services (hereinafter referred to as “the Company”, “we”, “us”, “our”) and the Client who orders Our Services (hereinafter referred to as “the Client”, “you”, “your”)</p>
        <!-- Agreement statement, indicating client's acceptance by placing an order. -->
        <p>By placing an order with Dinolabs Tech Services for website design, development and Software services, you confirm that you are in agreement with and bound by the terms and conditions below.</p>

        <h2 class="mt-3">Definitions</h2>
        <!-- Definition of "Terms and Conditions". -->
        <p>“Terms and Conditions”: These are the standard terms and conditions for Software as a Service (SaaS) and apply to all contracts and all design works undertaken by Dinolabs Tech Services for its clients.</p>
        <!-- Definition of "The Client". -->
        <p>“The Client”: The company or individual requesting the services of Dinolabs Tech Services.</p>
        <!-- Definition of "Dinolabs Tech Services". -->
        <p>“Dinolabs Tech Services”: A Web Hosting Company</p>

        <h2 class="pt-3">a. OUR FEES</h2>
        <!-- Clause regarding payment terms for services. -->
        <p>You are expected to pay the full amount for your Service fee before we commence the development of your Software. We reserve the right not to commence any work until we receive the full payment for your service except agreed otherwise.</p>

        <h2 class="pt-3">b. SUPPLY OF MATERIALS</h2>
        <!-- Clause regarding the client's responsibility to supply necessary materials. -->
        <p>You must supply everything we need to complete the project on your behalf including the format we need to complete the work in accordance with any agreed specification. Some of the materials to be supplied by you may include but are not limited to images, content, logos, and other printed material you would like to incorporate into our software provided as a service. In the event you delay in supplying these materials to us, which could eventually lead to a delay in the completion of work, we reserve the right to extend any previously agreed deadlines by a reasonable amount of time.</p>
        <!-- Consequence of client's failure to supply materials. -->
        <p>Where you fail to supply materials required to build your software, and it prevents the progress of the work leading to a stoppage of the work at hand, we reserve the right to invoice you for any part or parts of the work already completed.</p>

        <h2 class="pt-3">c. VARIATIONS</h2>
        <!-- Clause regarding design revisions and additional charges for major changes. -->
        <p>We are pleased to offer you the opportunity to make revisions to the design. However, we have the right to limit the number of your design proposals to a reasonable amount and Dinolabs Tech Services at its discretion may charge for additional designs if you make a change to the original design specification after approval has been given.</p>
        <!-- Distinction between minor and major changes and their cost implications. -->
        <p>Our software development phase is flexible and allows certain variations to the original specification. However, any major change from the specification will be charged at an additional cost. A minor change is anything that has to do with adjusting of size, change of placement, moving of components, color change, image replacement, or text change. A major change is an addition of a new feature which has direct or indirect effect on the existing database structure been agreed to by Dinolabs Tech Services. This does not however mean upgrades of unlimited complexity but within the measurable framework of the existing content management system.</p>

        <h2 class="pt-3">d. PROJECT CLAUSES (Please read through this carefully)</h2>
        <ul>
            <li>
                <h3 class="pt-2">PROJECT DELAYS AND CLIENT LIABILITY</h3>
                <!-- Clause on client cooperation and feedback for project timeliness. -->
                <p>Any time frames or estimates that we give are contingent upon your full cooperation, and submission of complete and final content for the software pages/elements. During development, there is a certain amount of feedback required in order to progress to subsequent phases. We require that you appoint and make available on a daily basis, a single point of contact in order to expedite the feedback process.</p>
            </li>
            <li>
                <h3 class="pt-2">APPROVAL OF WORK</h3>
                <!-- Clause on client review and approval process for completed work. -->
                <p>During the design and development phase and upon completion of the work, you will be notified and given the opportunity to review the appearance and content of the website. Upon completion of the project, you must notify us via mail of any unsatisfactory points within seven(7) days of such notification.</p>
                <!-- Consequence of not reporting unsatisfactory work within the review period. -->
                <p>Any of the work which has not been reported in writing to us as unsatisfactory within the 7-day review period will be deemed to have been accepted or approved. Once deemed approved or accepted, work cannot subsequently be rejected and the contract will be deemed to have been completed and moved live.</p>
            </li>
            <li>
                <h3 class="pt-2">REJECTED WORK</h3>
                <!-- Clause on the company's right to terminate the contract if work is unreasonably rejected. -->
                <p>If you reject any of our work within the 7-day review period or not approve subsequent work performed by us to remedy any points recorded as being unsatisfactory, and we, acting reasonably consider that you have been illogical/irrational in the decision, we can elect to treat this contract as terminated and take measures to recover payment for the completed work – service fees are not refundable.</p>
            </li>
            <li>
                <h3 class="pt-2">PAYMENT</h3>
                <!-- Clause on payment terms and invoice delivery. -->
                <p>Payment will be received in full before work commences and invoices will be received via the contact email address.</p>
            </li>
            <li>
                <h3 class="pt-2">WARRANTY BY YOU AS TO OWNERSHIP OF INTELLECTUAL PROPERTY RIGHTS</h3>
                <!-- Clause on client's responsibility for intellectual property rights of supplied materials. -->
                <p>You must obtain all necessary permissions and authorities in respect of the use of all graphic images, registered company logos, names, and trademarks, or any other material that you supply to us to include in your website or web applications.</p>
                <!-- Client's indemnification of the company against IP claims. -->
                <p>You must indemnify us and hold us harmless from any claims or legal actions related to the content of your website.</p>
            </li>
            <li>
                <h3 class="pt-2">LICENSING</h3>
                <!-- Clause on the license granted to the client upon full payment. -->
                <p>Once you have paid us in full for our work we grant you a license to use the software and its related contents for the life of your subscription with us.</p>
            </li>
            <li>
                <h3 class="pt-2">SEARCH ENGINES</h3>
                <!-- Disclaimer regarding search engine rankings. -->
                <p>We do not guarantee any specific position in search engine results for your website. We perform basic search engine optimization integration according to current best practices.</p>
            </li>
            <li>
                <h3 class="pt-2">OWNERSHIP TRANSFER</h3>
                <!-- Clause on ownership of website components after project completion and payment. -->
                <p>Upon completion of the project, and payment, the Client shall own the website subject to the following terms and conditions:</p>
                <ol>
                    <!-- Ownership of client-supplied artwork and content. -->
                    <li>All original artwork, content, etc. provided to the Company by you for inclusion in the website shall remain your exclusive property, regardless of whether or not such content was actually used in the website;</li>
                    <!-- Ownership of company-created photography, graphics, and design. -->
                    <li>All photography, graphics, and design created by the Company in the creation of your website shall on full payment, transfer to you as your property;</li>
                </ol>
            </li>
            <li>
                <h3 class="pt-2">SUBCONTRACTING</h3>
                <!-- Clause on the company's right to subcontract services. -->
                <p>We reserve the right to subcontract any services that we have agreed to perform for you as we see fit.</p>
            </li>
            <li>
                <h3 class="pt-2">ADDITIONAL EXPENSES</h3>
                <!-- Clause on client's agreement to reimburse for additional expenses. -->
                <p>You agree to reimburse us for any requested expenses which do not form part of our proposal including but not limited to the purchase of templates, third-party software, stock photographs, fonts, domain name registration, web hosting, or comparable expenses.</p>
            </li>
            <li>
                <h3 class="pt-2">BACKUPS</h3>
                <!-- Clause on client's responsibility for backups and company's liability. -->
                <p>You are responsible for maintaining your own backups with respect to your website and we will not be liable for any loss in client data or client websites except to the extent that such data loss arises out of a negligent act or omission by us.</p>
            </li>
            <li>
                <h3 class="pt-2">OWNERSHIP OF DOMAIN NAMES AND WEB HOSTING</h3>
                <!-- Clause on providing account credentials for domain and hosting upon full payment. -->
                <p>We will supply your account credentials for domain name registration and/or web hosting if payment is made in full.</p>
            </li>
            <li>
                <h3 class="pt-2">CROSS-BROWSER COMPATIBILITY</h3>
                <!-- Clause on ensuring cross-browser compatibility and handling third-party extensions. -->
                <p>We endeavor to ensure that the websites we create are compatible with all current modern web browsers such as the most recent versions of Microsoft Edge, Internet Explorer, Firefox, Google Chrome, and Safari. Third-party extensions, where used, may not have the same level of support for all browsers. Where appropriate we will substitute alternative extensions or implement other solutions, on a best-effort basis, where any incompatibilities are found.</p>
            </li>
            <li>
                <h3 class="pt-2">E-COMMERCE</h3>
                <!-- Clause on client's responsibility for e-commerce laws and indemnification. -->
                <p>You are responsible for complying with all relevant laws relating to e-commerce, and to the full extent permitted by law will hold harmless, protect, and defend and indemnify Dinolabs Tech Services and its subcontractors from any claim, penalty, tax, tariff loss, or damage arising from your or your clients' use of Internet electronic commerce.</p>
            </li>
            <li>
                <h3 class="pt-2">PROJECT TIMELINESS REQUIREMENTS FOR CLIENTS</h3>
                <!-- Clause on client's role in project timeliness and consequences of delays. -->
                <p>Projects can be hindered if you do not provide feedback or required elements in a timely manner, such as feedback on a design mockup, requested sitemaps, text to be used as content on the web pages, photos for either the design or for the content, the client's logo, appropriate account login information, etc. For that reason, if Dinolabs Tech Services Limited's Development team is waiting for content or other pieces of information, the client will be notified.</p>
                <!-- Consequences of prolonged client delays. -->
                <p>If you fail to handle the requests within 3-5 business days, the project timeline will be moved forward. However, if you fail to handle the requests within ten business days, the project will be frozen and you will be required to pay an additional fee to resume the project.</p>
            </li>
            <li>
                <h3 class="pt-2">DISCOUNTS</h3>
                <!-- Clause on conditions for receiving discounts. -->
                <ol>
                    <!-- Discount for logos with website development/management service. -->
                    <li>Logos: This is created at a discounted rate only for clients who have also subscribed to our website development/management service.</li>
                    <!-- Discount for branding websites with Dinolabs Tech Services name. -->
                    <li>Branding: This discount applies to clients who agree to brand their websites with our business name by way of permitting our business name and URL to be on their website.</li>
                </ol>
            </li>
            <li>
                <h3 class="pt-2">REFUND POLICY</h3>
                <!-- Clause on refund policy for website development and graphics design. -->
                <p>There shall be no refund of payment for any website development job once a website template has been chosen as the third-party cost has been incurred.</p>
                <!-- Partial refund for graphics design. -->
                <p>Graphics Design: The client is entitled to a partial refund if not satisfied with our design after four reviews. This is subject to only Logos and any graphics design.</p>
            </li>
            <li>
                <h3 class="pt-2">TERMINATION OF SERVICES</h3>
                <!-- Clause on conditions for termination of services. -->
                <p>This service shall be terminated with/without notice If we do not get a response from you regarding your website development within six (6) months.</p>
                <!-- Client's option to terminate during initial design/development phase. -->
                <p>If you wish to terminate this service during the course of the initial web design or development phase, kindly notify the web development team stating your reasons for termination.</p>
            </li>
            <li>
                <h3 class="pt-2">WEBSITE MAINTENANCE</h3>
                <!-- Clause on free minor maintenance period post web development. -->
                <p>We shall provide minor maintenance to your web pages over a period of twenty (20) working days post web development, including updating lines and making minor changes to a sentence or paragraph. This maintenance does not include updating or replacing nearly all the text from a page with new text, major page reconstruction, new pages, guest books, discussion webs, and navigation structure changes, attempted updates by client repairs, or web design projects delivered to the client. The twenty (20) working days maintenance period shall begin on the date your website has been published to your hosting service.</p>
                <!-- Maintenance for database access and server-side scripts. -->
                <p>Note however that if your web design package includes database access using Server Side Script, then very minor page code changes will be accepted under this maintenance plan. Major page code and/or database structural changes will be charged accordingly.</p>
                <!-- Options after the free maintenance period. -->
                <p>After the free twenty (20) working days maintenance phase, you can either subscribe to our Web Management Service or contact the Development Team.</p>
            </li>
            <li>
                <h3 class="pt-2">LIMITATION OF LIABILITY</h3>
                <!-- Clause on the company's limitation of liability for errors and damages. -->
                <p>In no event shall the liability of the Company for any error made in the performance of the obligations under these terms of service exceed the fees for such services paid to the Company by the Client. The Company shall not be liable to the Client or to any end-user for any damages including, without limitation, consequential damages, lost profits, or any special damages, whether incurred by the Client or end-user.</p>
            </li>
            <li>
                <h3 class="pt-2">NON-DISCLOSURE</h3>
                <!-- Clause on non-disclosure of client's confidential information. -->
                <p>We (and any subcontractors we engage) agree that we will not at any time disclose any of your confidential information to any third party.</p>
            </li>
            <li>
                <h3 class="pt-2">DISCLAIMER</h3>
                <!-- Disclaimer regarding warranties and performance of web pages/website. -->
                <p>DINOLABS TECH SERVICES DOES NOT WARRANT THAT THE FUNCTIONS CONTAINED IN THE WEB PAGES OR THE WEBSITE WILL MEET THE CLIENT’S REQUIREMENTS OR THAT THE OPERATION OF THE WEB PAGES WILL BE UNINTERRUPTED OR ERROR-FREE. THE ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE WEB PAGES AND WEBSITE SHALL BE ON THE CLIENT. EXCEPT AS OTHERWISE SPECIFIED IN THIS AGREEMENT, Dinolabs Tech Services PROVIDES ITS SERVICES “AS IS'' AND WITHOUT WARRANTY OF ANY KIND. THE PARTIES AGREE THAT (A) THE LIMITED WARRANTIES SET FORTH IN THIS SECTION ARE THE SOLE AND EXCLUSIVE WARRANTIES PROVIDED BY EACH PARTY, AND (B) EACH PARTY DISCLAIMS ALL OTHER WARRANTIES, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE, RELATING TO THIS AGREEMENT, PERFORMANCE OR INABILITY TO PERFORM UNDER THIS AGREEMENT, THE CONTENT, AND EACH PARTY’S COMPUTING AND DISTRIBUTION SYSTEM. IF ANY PROVISION OF THIS AGREEMENT SHALL BE UNLAWFUL, VOID, OR FOR ANY REASON UNENFORCEABLE, THEN THAT PROVISION SHALL BE DEEMED SEVERABLE FROM THIS AGREEMENT AND SHALL NOT AFFECT THE VALIDITY AND ENFORCEABILITY OF ANY REMAINING PROVISIONS.</p>
            </li>
            <li>
                <h3 class="pt-2">GOVERNING LAW AND DISPUTE RESOLUTION</h3>
                <!-- Clause on the governing law and dispute resolution process (arbitration). -->
                <p>This agreement is constituted by these terms and conditions and any proposal will be construed according to and is governed by the laws of the Federal Republic of Nigeria. In the event of a dispute between the Company and Client with respect to any issue arising out of or relating to this Agreement in any manner, including but not limited to the breach thereof, parties shall endeavor to resolve such dispute amicably between themselves within thirty (30) days, failing which resolution of such dispute shall be determined by Arbitration. Such arbitration shall be conducted before an arbitrator chosen as follows: either the Company and Client shall agree on a mutually acceptable arbitrator, or the Company shall select one arbitrator and Client shall select one arbitrator, and these two arbitrators shall choose a third arbitrator who will act as arbitrator hereunder. The arbitrator’s decision shall be final and binding upon all parties concerned. Such decisions shall be rendered within thirty (30) days of the closing of the hearing record. The arbitration proceedings conducted hereunder shall be conducted in English Language and in , Nigeria, and each party shall bear its own costs. The arbitration shall be conducted in accordance with the rules and provisions of the Arbitration and Conciliation Act Cap A18, Laws of the Federal Republic of Nigeria 2004. Judgment upon the award rendered by the arbitrator(s) shall be entered in any court of competent jurisdiction.</p>
            </li>
        </ul>

</div>

<!-- Start Footer Area -->
    <?php include('components/footer.php') ?>
    <!-- /End Footer Area -->

    <!-- Jquery -->
    <?php include('components/script.php') ?>
</body>
</html>