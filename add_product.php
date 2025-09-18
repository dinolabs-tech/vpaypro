<?php
session_start();
require_once 'db_connect.php'; // Make sure this uses MySQLi


if (!isset($_SESSION['staffname'])) {
    header("Location: index.php");
    exit;
}

// Fetch categories using MySQLi
$categories = [];
$result = $conn->query("SELECT * FROM categories");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $_POST['price'];
    $discount_price = $_POST['discount_price'];
    $discount_end_date = $_POST['discount_end_date'];
    $category_id = $_POST['category_id'];
    $stock = $_POST['stock'];
    $sku = $_POST['sku'];
    $weight = $_POST['weight'];
    $dimensions = $_POST['dimensions'];
    $material = $_POST['material'];
    $color = $_POST['color'];
    $brand = $_POST['brand'];

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, discount_price, discount_end_date, category_id, stock, sku, weight, dimensions, material, color, brand) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddsisidssss", $name, $description, $price, $discount_price, $discount_end_date, $category_id, $stock, $sku, $weight, $dimensions, $material, $color, $brand);
    $stmt->execute();
    $product_id = $stmt->insert_id;
    $stmt->close();

    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $target_dir = "assets/images/";
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_extension = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
            $image_name = date('YmdHis') . '_' . uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $image_name;

            if (move_uploaded_file($tmp_name, $target_file)) {
                $stmt_image = $conn->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
                $stmt_image->bind_param("is", $product_id, $image_name);
                $stmt_image->execute();
                $stmt_image->close();
            }
        }
    }

    header('Location: products.php');
    exit;
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
                            <li class="active">Add New Product</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <div class="container pt-3">
        <!-- <h1>Add New Product</h1> -->
        <div class="form-main">
            <form class="form" action="add_product.php" method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input class="form-control rounded px-3 py-2" type="text" name="name" id="name" placeholder="Name" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <textarea class="form-control rounded px-3 py-2" name="description" id="description" placeholder="Description" required></textarea><br>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input class="form-control rounded px-3 py-2" type="text" name="price" id="price" placeholder="Price" required><br>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input class="form-control rounded px-3 py-2" type="text" name="discount_price" id="discount_price" placeholder="Discount Price">
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input class="form-control rounded px-3 py-2" type="datetime-local" name="discount_end_date" id="discount_end_date">
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <select class="form-control form-select w-100 rounded px-3 py-2" name="category_id" id="category_id" required>
                                <option value="" selected disabled>Select category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input placeholder="Stock" class="form-control rounded px-3 py-2" type="number" name="stock" id="stock" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input placeholder="SKU" class="form-control rounded px-3 py-2" type="text" name="sku" id="sku"><br>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input placeholder="Weight (kg)" class="form-control rounded px-3 py-2" type="text" name="weight" id="weight">
                        </div>
                    </div>
                    <!-- <label for="dimensions">Dimensions</label> -->

                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input placeholder="Dimensions" class="form-control rounded px-3 py-2" type="text" name="dimensions" id="dimensions">
                        </div>
                    </div>
                    <!-- <label for="material">Material</label> -->

                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input placeholder="Material" class="form-control rounded px-3 py-2" type="text" name="material" id="material">
                        </div>
                    </div>
                    <!-- <label for="color">Color</label> -->

                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input placeholder="Color" class="form-control rounded px-3 py-2" type="text" name="color" id="color">
                        </div>
                    </div>
                    <!-- <label for="brand">Brand</label> -->

                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input placeholder="Brand" class="form-control rounded px-3 py-2" type="text" name="brand" id="brand">
                        </div>
                    </div>
                    <!-- <label for="image">Image</label> -->
                    
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <label for="images">Product Images</label>
                            <input class="px-3 py-2" type="file" name="images[]" id="images" multiple>
                            <button type="submit" class="btn btn-primary rounded mt-3 text-white">Add Product</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Start Footer Area -->
    <?php include('components/footer.php') ?>
    <!-- /End Footer Area -->

    <!-- Jquery -->
    <?php include('components/script.php') ?>
</body>

</html>
