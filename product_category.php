<?php
session_start();
include('db_connect.php');
include('includes/functions.php');

$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

if ($category_id > 0) {
    // Fetch category details
    $sql_category = "SELECT * FROM categories WHERE id = $category_id";
    $result_category = $conn->query($sql_category);
    $category = $result_category->fetch_assoc();

    // Fetch products in the category
    $sql_products = "SELECT p.*, pi.image_path, AVG(r.rating) as avg_rating
        FROM products p
        LEFT JOIN reviews r ON p.id = r.product_id
        INNER JOIN product_images pi on p.id=pi.product_id
        WHERE p.category_id = $category_id
        GROUP BY p.id";
    $result_products = $conn->query($sql_products);
} else {
    // Redirect or show an error if no category is specified
    header("Location: index.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php') ?>

<body class="js">

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
                            <li class="active">Product Category</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Product Style 1 -->

        <div class="container">
        <div class="product-list p-3">
            <?php if ($result_products->num_rows > 0): ?>
                <?php foreach ($result_products as $product): ?>
                    <div class="product-card">
                        <a href="product_details.php?id=<?php echo $product['id']; ?>">
                            <h3 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <img class="rounded" style="width: 400px; height:300px;" src="assets/images/<?php echo $product['image_path']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="rating mt-3 mb-3"><?php echo generate_stars($product['avg_rating']); ?></div>
                            <?php if ($product['discount_price'] > 0 && (empty($product['discount_end_date']) || $product['discount_end_date'] > date('Y-m-d H:i:s'))): ?>
                                <h6 class="discount-price">$<?php echo $product['discount_price']; ?></h6>
                                <p class="original-price"><small><s>$<?php echo $product['price']; ?></s></small></p>
                                <?php
                                $percentage_reduction = (($product['price'] - $product['discount_price']) / $product['price']) * 100;
                                echo '<h6 class="percentage-reduction mb-3">' . round($percentage_reduction) . '% off</h6>';
                                ?>
                            <?php else: ?>
                                <p class="mt-3"><h2>$<?php echo $product['price']; ?></h2></p>
                            <?php endif; ?>
                        </a>
                        <a class="text-white btn rounded fa fa-shopping-cart mb-3 px-3 mt-3" style="font-size:24px;margin-right:10px;" href="cart.php?add=<?php echo $product['id']; ?>" class="btn"></a>
                        <a class="text-white btn rounded fa fa-heart mb-3 px-3" style="font-size:24px;margin-right:10px;" href="wishlist.php?add=<?php echo $product['id']; ?>" class="btn"></a>
                        <a class="text-white btn rounded ti-bar-chart-alt mb-3 px-3" style="font-size:24px;margin-right:10px;" href="compare.php?add=<?php echo $product['id']; ?>" class="btn"></a>
                        <a data-toggle="modal" data-target="#exampleModal" title="Quick View" href="javascript:void(0);" class="btn quick-view-button rounded" style="font-size:24px;" data-id="<?php echo $product['id']; ?>" ><i class="text-white fa fa-eye"></i></a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products found in this category.</p>
            <?php endif; ?>
        </div>
        </div>
    <!--/ End Product Style 1  -->
    <!-- Start Footer Area -->
    <?php include('components/footer.php') ?>
    <!-- /End Footer Area -->


    <!-- Jquery -->
    <?php include('components/script.php') ?>
</body>

</html>