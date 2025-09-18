<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

include("db_connect.php");
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
                            <li class="active">Dashboard</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Start Blog Single -->
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-6 col-md-6">
                    <div class="service-item bg-light rounded d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="service-icon">
                            <i class="fa fa-shield-alt"></i>
                        </div>
                        <h4 class="mb-3 pt-5">Total Posts</h4>

                        <?php
                        $sql_posts = "SELECT COUNT(*) AS total_posts FROM posts";
                        $result_posts = $conn->query($sql_posts);
                        $posts_data = $result_posts->fetch_assoc();
                        $total_posts = $posts_data["total_posts"];
                        ?>
                        <p class="card-text pb-5"><?php echo $total_posts; ?></p>

                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="service-item bg-light rounded d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="service-icon">
                            <i class="fa fa-shield-alt text-white"></i>
                        </div>
                        <h4 class="mb-3 pt-5">Total Comments</h4>

                        <?php
                        $sql_comments = "SELECT COUNT(*) AS total_comments FROM comments";
                        $result_comments = $conn->query($sql_comments);
                        $comments_data = $result_comments->fetch_assoc();
                        $total_comments = $comments_data["total_comments"];
                        ?>
                        <p class="card-text pb-5"><?php echo $total_comments; ?></p>


                    </div>
                </div>


            </div>
        </div>
    </div>
    <!--/ End Blog Single -->

    <!-- Start Footer Area -->
    <?php include('components/footer.php') ?>
    <!-- /End Footer Area -->

    <!-- Jquery -->
    <?php include('components/script.php') ?>
</body>

</html>