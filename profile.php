<?php

session_start();
require_once 'db_connect.php';
// require_once '../../includes/db.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$sql_user = "SELECT * from users WHERE id = $user_id";
$result_user = $conn->query($sql_user);
$user = $result_user->fetch_assoc(); // Fetch a single row

$sql_orders = "SELECT * FROM orders WHERE customer_email = (SELECT email FROM users WHERE id = ?)";
$stmt = $conn->prepare($sql_orders);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$orders = $stmt->get_result();
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
                            <li class="active">Profile</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <div class="container p-5">
        <?php if (isset($_GET['order_success']) && $_GET['order_success'] == 'true'): ?>
            <div class="alert alert-success">Your order has been placed successfully!</div>
        <?php endif; ?>
        <?php if (isset($_GET['profile_updated']) && $_GET['profile_updated'] == 'true'): ?>
            <div class="alert alert-success">Your profile has been updated successfully!</div>
        <?php endif; ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>
                            <p><strong>First Name:</strong></p>
                        </th>
                        <td><?php echo $user['first_name']; ?></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>
                            <p><strong>Last Name:</strong></p>
                        </th>
                        <td><?php echo $user['last_name']; ?></td>
                    </tr>
                    <tr>
                        <th>
                            <p><strong>Username:</strong></p>
                        </th>
                        <td><?php echo $user['username']; ?></td>
                    </tr>
                    <tr>
                        <th>
                            <p><strong>Email:</strong></p>
                        </th>
                        <td><?php echo $user['email']; ?></td>
                    </tr>
                    <tr>
                        <th>
                            <p><strong>Contact:</strong></p>
                        </th>
                        <td><?php echo $user['contact']; ?></td>
                    </tr>
                    <tr>
                        <th>
                            <p><strong>Address:</strong></p>
                        </th>
                        <td><?php echo $user['address']; ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button id="editProfileBtn" class="btn btn-primary rounded mb-3">Edit Profile</button>

        <div id="editProfileForm" style="display: none;">
            <h2 class="mt-5">Edit Profile</h2>
            <form action="update_profile.php" method="POST">
                <div class="row g-3">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <div class="form-group col-md-4">
                        <div class="input-group">
                            <span class="input-group-text text-center">First Name:&nbsp;</span>
                            <input type="text" class="form-control px-2" id="first_name" name="first_name" value="<?php echo $user['first_name']; ?>" required>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <div class="input-group">
                            <span class="input-group-text text-center">Last Name:&nbsp;</span>
                            <input type="text" class="form-control px-2" id="last_name" name="last_name" value="<?php echo $user['last_name']; ?>" required>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <div class="input-group">
                        <span class="input-group-text text-center">Username:&nbsp;</span>
                        <input type="text" class="form-control px-2" id="username" name="username" value="<?php echo $user['username']; ?>" required>
                    </div>
                    </div>
                    <div class="form-group col-md-4">
                        <div class="input-group">
                        <span class="input-group-text text-center">Email:&nbsp;</span>
                        <input type="email" class="form-control px-2" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                    </div>
                    </div>
                    <div class="form-group col-md-4">
                        <div class="input-group">
                        <span class="input-group-text text-center">Contact:&nbsp;</span>
                        <input type="text" class="form-control px-2" id="contact" name="contact" value="<?php echo $user['contact']; ?>">
                    </div>
                    </div>
                    <div class="form-group col-md-4">
                        <div class="input-group">
                        <span class="input-group-text text-center">Address:&nbsp;</span>
                        <textarea class="form-control px-2" id="address" name="address"><?php echo $user['address']; ?></textarea>
                    </div>
                    </div>
                    <button type="submit" class="btn btn-success rounded" style="margin-left:10px; margin-right:10px;">Save Changes</button>
                    <button type="button" id="cancelEditBtn" class="btn btn-secondary rounded">Cancel</button>
                </div>
            </form>
        </div>

        <h2 class="mt-5">My Orders</h2>
        <?php if ($orders->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Total</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td>
                            <td>$<?php echo $order['total']; ?></td>
                            <td><?php echo $order['created_at']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="mt-3">You have no orders.</p>
        <?php endif; ?>
    </div>

    <script src="js/profile_edit.js"></script>
    <?php include('components/footer.php') ?>
    <?php include('components/script.php') ?>
</body>

</html>