<?php
session_start();

include 'db_connect.php';



if(isset($_POST['update_order'])){

   $order_id = $_POST['order_id'];
   $update_payment = $_POST['update_payment'];
   $update_payment = filter_var($update_payment, FILTER_SANITIZE_STRING);
   $stmt = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $stmt->bind_param("si", $update_payment, $order_id);
   $stmt->execute();
   $message[] = 'payment has been updated!';
   $stmt->close();

}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $stmt = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $stmt->bind_param("i", $delete_id);
   $stmt->execute();
   $stmt->close();
   header('location:orders.php');
}

?>

<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php') ?>

<body class="js">



	<!-- Header -->
	<?php include('components/header.php'); ?>
	<!--/ End Header -->

	<!-- Breadcrumbs -->
	<div class="breadcrumbs">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="bread-inner">
						<ul class="bread-list">
							<li><a href="index.php">Home<i class="ti-arrow-right"></i></a></li>
							<li class="active">Orders</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->


<section class="container placed-orders pt-2">

   <h1 class="title">Placed Orders</h1>

   <div class="box-container">

      <?php
         $select_orders = $conn->prepare("SELECT * FROM `orders`");
         $select_orders->execute();
         $result = $select_orders->get_result();
         if($result->num_rows > 0){
            while($fetch_orders = $result->fetch_assoc()){
      ?>
      <div class="box">
         <p> User id : <span><?= $fetch_orders['user_id']; ?></span> </p>
         <p> Placed on : <span><?= $fetch_orders['placed_on']; ?></span> </p>
         <p> Name : <span><?= $fetch_orders['name']; ?></span> </p>
         <p> Email : <span><?= $fetch_orders['email']; ?></span> </p>
         <p> Number : <span><?= $fetch_orders['number']; ?></span> </p>
         <p> Address : <span><?= $fetch_orders['address']; ?></span> </p>
         <p> Total Products : <span><?= $fetch_orders['total_products']; ?></span> </p>
         <p> Total Price : <span>$<?= $fetch_orders['total_price']; ?>/-</span> </p>
         <p> Payment Method : <span><?= $fetch_orders['method']; ?></span> </p>
         <form action="" method="POST">
            <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
            <select name="update_payment" class="drop-down">
               <option value="" selected disabled><?= $fetch_orders['payment_status']; ?></option>
               <option value="pending">Pending</option>
               <option value="completed">Completed</option>
            </select>
            <input type="submit" name="update_order" class="btn rounded text-white" value="update">
            <a href="orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('delete this order?');">Delete</a>
         </form>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">No orders placed yet!</p>';
      }
      ?>

   </div>

</section>

<?php include('components/footer.php'); ?>

<?php include('components/script.php'); ?>
</body>
</html>