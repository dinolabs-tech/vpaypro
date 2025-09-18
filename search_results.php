<?php
session_start();
include('db_connect.php');
include('includes/functions.php'); // Assuming you have a functions.php for general functions

$search_query = isset($_GET['search_query']) ? trim($_GET['search_query']) : '';
$products = [];

if (!empty($search_query)) {
    $stmt = $conn->prepare("SELECT * FROM product
     WHERE productname LIKE ? OR description LIKE ?");
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
}
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
                            <li class="active">Search Results</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->



    <div class="container p-5">
        <div class="row">


<?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
            <div class="col-lg-4 col-md-6 col-12">
                
                        <div class="single-product">
                            <div class="product-img">
                                <a href="product_details.php?id=<?php echo $product['productid']; ?>">
                                    <img class="img-fluid rounded" src="backend/<?= $product['image_url']; ?>" alt="<?= $product['productname']; ?>" height="60px" width="60px">

                                </a>
                                <div class="button-head">
                                    <div class="product-action">
                                        <a title="Wishlist" href="wishlist.php?add=<?= $product['productid']; ?>"><i class=" ti-heart "></i><span>Add to Wishlist</span></a>
                                        <a title="Compare" href="compare.php?add=<?= $product['productid']; ?>"><i class="ti-bar-chart-alt"></i><span>Compare</span></a>
                                    </div>
                                    <div class="product-action-2">
                                        <a title="Add to Cart" href="cart.php?add=<?= $product['productid']; ?>" class="btn rounded text-dark">Add to Cart</a>
                                    </div>
                                </div>
                            </div>
                            <div class="product-content">
                                <h3><a href="product_details.php"><?= $product['productname']; ?></a></h3>

                                <div class="product-price">
                                    <span>$<?php echo htmlspecialchars(number_format($product['sellprice'], 2)); ?></span>
                                </div>

                            </div>
                        </div>
                   
            </div>
 <?php endforeach; ?>
                <?php else: ?>
                    <p>No products found matching your search criteria.</p>
                <?php endif; ?>
        </div>
    </div>

    	<!-- Start Footer Area -->
	<?php include('components/footer.php') ?>
	<!-- /End Footer Area -->


	<!-- Jquery -->
	<?php include('components/script.php') ?>
</body>

</html>