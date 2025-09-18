<?php
session_start();
require_once 'db_connect.php';
require_once 'includes/functions.php';


if (!isset($_SESSION['compare_items'])) {
    $_SESSION['compare_items'] = [];
}

// Handle add to compare
if (isset($_GET['add'])) {
    $product_id = $_GET['add'];
    if (!in_array($product_id, $_SESSION['compare_items'])) {
        $_SESSION['compare_items'][] = $product_id;
    }
    header('Location: compare.php');
    exit;
}

// Handle remove from compare
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    $_SESSION['compare_items'] = array_diff($_SESSION['compare_items'], [$product_id]);
    header('Location: compare.php');
    exit;
}

$compare_items = [];
if (!empty($_SESSION['compare_items'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['compare_items']), '?'));
    $types = str_repeat('i', count($_SESSION['compare_items']));
    $sql = "
        SELECT 
            p.*
        FROM product p
        WHERE p.productid IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$_SESSION['compare_items']);
    $stmt->execute();
    $compare_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php')?>
<body>
		
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
								<li class="active">Compare</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Breadcrumbs -->
		
		<div class="container table table-bordered mt-5 pt-5">
        <?php if (empty($compare_items)): ?>
            <p>There are no products to compare.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Feature</th>
                        <?php foreach ($compare_items as $item): ?>
                            <th><?php echo $item['productname']; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Image</td>
                        <?php foreach ($compare_items as $item): ?>
                            <td>
                                <?php $image_src = !empty($item['image_url']) ? htmlspecialchars($item['image_url']) : 'assets/images/default.jpg'; ?>
                                <img src="backend/<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($item['productname']); ?>" width="100">
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td>Price</td>
                        <?php foreach ($compare_items as $item): ?>
                            <td>$<?php echo $item['sellprice']; ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td>Description</td>
                        <?php foreach ($compare_items as $item): ?>
                            <td><?php echo $item['description']; ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td>Action</td>
                        <?php foreach ($compare_items as $item): ?>
                            <td>
                                <a class="btn rounded mb-3 px-3 fa fa-trash text-white" style="font-size: 18px" href="compare.php?remove=<?php echo $item['productid']; ?>"></a>
                                <a class="btn rounded px-3 fa fa-shopping-cart text-white" style="font-size: 18px" href="cart.php?add=<?php echo $item['productid']; ?>"></a>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>	
		<!-- Start Footer Area -->
	<?php include('components/footer.php')?>
		<!-- /End Footer Area -->
	
	
	<!-- Jquery -->
  <?php include('components/script.php')?>
</body>
</html>
