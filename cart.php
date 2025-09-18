<?php
session_start();
require_once 'db_connect.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle add to cart
if (isset($_GET['add'])) {
    $product_id = $_GET['add'];
    $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

    $sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $cart_item = $stmt->get_result()->fetch_assoc();

    if ($cart_item) {
        $new_quantity = $cart_item['quantity'] + $quantity;
        $sql = "UPDATE cart SET quantity = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $new_quantity, $cart_item['id']);
        $stmt->execute();
    } else {
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $stmt->execute();
    }
    header('Location: cart.php');
    exit;
}

// Handle update cart
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        if ($quantity == 0) {
            $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
        } else {
            $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $quantity, $user_id, $product_id);
            $stmt->execute();
        }
    }
    header('Location: cart.php');
    exit;
}

// Handle remove from cart
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    header('Location: cart.php');
    exit;
}

$sql = "
    SELECT 
        p.*, 
        c.quantity,
        (SELECT image_path FROM product_images WHERE product_id = p.id LIMIT 1) as primary_image
    FROM products p
    JOIN cart c ON p.id = c.product_id 
    WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
							<li class="active">Cart</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->
			
	<!-- Shopping Cart -->
	
	<!--/ End Shopping Cart -->
			
	<div class="container pt-3">
       
        <?php if (empty($cart_items)): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <form action="cart.php" method="post">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($cart_items as $item):
                            $item_total = $item['price'] * $item['quantity'];
                            $total += $item_total;
                        ?>
                            <tr>
                                <td>
                                    <?php $image_src = !empty($item['primary_image']) ? htmlspecialchars($item['primary_image']) : 'default.jpg'; ?>
                                    <img style="margin-right: 10px;" src="assets/images/<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" width="50">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </td>
                                <td>$<?php echo htmlspecialchars($item['price']); ?></td>
                                <td><input type="number" name="quantity[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="0"></td>
                                <td>$<?php echo number_format($item_total, 2); ?></td>
                                <td><a class="fa fa-trash rounded btn text-white" href="cart.php?remove=<?php echo $item['id']; ?>"></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Total</th>
                            <th>$<?php echo number_format($total, 2); ?></th>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <button type="submit"class="btn rounded text-white" name="update_cart">Update Cart</button>
            </form>
            <a href="checkout.php" class="btn rounded mt-1 text-white">Proceed to Checkout</a>
        <?php endif; ?>
    </div>
	
	<!-- Start Footer Area -->
<?php include('components/footer.php')?>
	<!-- /End Footer Area -->
	
	<!-- Jquery -->
<?php include('components/script.php')?>
</body>
</html>
