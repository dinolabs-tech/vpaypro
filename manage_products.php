<?php
session_start();
require_once 'db_connect.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['staffname'])) {
    header("Location: login.php");
    exit;
}

$products_query = "
    SELECT 
        p.*, 
        (SELECT image_path FROM product_images WHERE product_id = p.id LIMIT 1) as primary_image
    FROM 
        products p
";
$products = $conn->query($products_query)->fetch_all(MYSQLI_ASSOC);
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
							<li class="active">Manage Products</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->
			
	<!-- Shopping Cart -->
	
	<!--/ End Shopping Cart -->
			
	<div class="container">
        <a class="fa fa-plus mt-3 mb-3 px-3 py-2 btn rounded text-white" style="font-size: 24px" href="add_product.php"></a>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Discount Price</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['price']; ?></td>
                        <td><?php echo $product['discount_price']; ?></td>
                        <td>
                            <?php $image_src = !empty($product['primary_image']) ? htmlspecialchars($product['primary_image']) : 'default.jpg'; ?>
                            <img class="rounded" src="assets/images/<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width:5vw; height:10vh;">
                        </td>
                        <td>
                            <a class="fa fa-edit mb-3 px-3 btn rounded text-white" style="font-size: 18px" href="edit_product.php?id=<?php echo $product['id']; ?>"></a>
                            <a class="fa fa-trash mb-3 px-3 btn rounded text-white" style="font-size:18px" href="delete_product.php?id=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure?')"></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </div>
	
	<!-- Start Footer Area -->
<?php include('components/footer.php')?>
	<!-- /End Footer Area -->
	
	<!-- Jquery -->
<?php include('components/script.php')?>
</body>
</html>
