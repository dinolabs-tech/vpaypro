<?php
session_start();
require_once 'db_connect.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle add to wishlist
if (isset($_GET['add'])) {
    $product_id = $_GET['add'];
    // Check if already in wishlist
    $sql = "SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        $sql = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
    }
    header('Location: wishlist.php');
    exit;
}

// Handle remove from wishlist
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    $sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    header('Location: wishlist.php');
    exit;
}

$sql = "
    SELECT 
        *
    FROM product p
    JOIN wishlist w ON p.productid = w.product_id 
    WHERE w.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$wishlist_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
							<li class="active">Wishlist</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->
			
	<!-- Shopping Cart -->
	
	<!--/ End Shopping Cart -->
			
	<div class="container mt-3">
        <?php if (empty($wishlist_items)): ?>
            <p>Your wishlist is empty.</p>
        <?php else: ?>
            <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($wishlist_items as $item): ?>
                        <tr>
                            <td>
                                <?php $image_src = !empty($item['image_url']) ? htmlspecialchars($item['image_url']) : 'assets/images/default.jpg'; ?>
                                <img class="rounded" style="margin-right: 10px; height:50px; width:50px;" src="backend/<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($item['productname']); ?>" width="50">
                                <?php echo htmlspecialchars($item['productname']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['sellprice']); ?></td>
                            <td>
                                <a class="fa fa-trash mb-3 px-3 btn rounded text-white" href="wishlist.php?remove=<?php echo $item['id']; ?>"></a>
                                <a class="fa fa-shopping-cart mb-3 px-3 btn rounded text-white" href="backend/online_store.php?add=<?php echo $item['id']; ?>"></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>
	
	<!-- Start Footer Area -->
<?php include('components/footer.php')?>
	<!-- /End Footer Area -->
	
	<!-- Jquery -->
<?php include('components/script.php')?>
</body>
</html>
