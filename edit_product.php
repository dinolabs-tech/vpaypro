<?php
session_start();
require_once 'db_connect.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['staffname'])) {
    header("Location: login.php");
    exit;
}
$product_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

$categories = $conn->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
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

    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, discount_price = ?, discount_end_date = ?, category_id = ?, stock = ?, sku = ?, weight = ?, dimensions = ?, material = ?, color = ?, brand = ? WHERE id = ?");
    $stmt->bind_param("ssddsisidssssi", $name, $description, $price, $discount_price, $discount_end_date, $category_id, $stock, $sku, $weight, $dimensions, $material, $color, $brand, $product_id);
    $stmt->execute();
    $stmt->close();

    // Handle new image uploads
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

    // Handle image deletions
    if (isset($_POST['delete_images'])) {
        foreach ($_POST['delete_images'] as $image_id) {
            // Get image path before deleting from DB
            $stmt_get_image = $conn->prepare("SELECT image_path FROM product_images WHERE id = ?");
            $stmt_get_image->bind_param("i", $image_id);
            $stmt_get_image->execute();
            $result_get_image = $stmt_get_image->get_result();
            $image_to_delete = $result_get_image->fetch_assoc();
            $stmt_get_image->close();

            if ($image_to_delete) {
                $file_path = "assets/images/" . $image_to_delete['image_path'];
                if (file_exists($file_path)) {
                    unlink($file_path); // Delete file from server
                }
                $stmt_delete_image = $conn->prepare("DELETE FROM product_images WHERE id = ?");
                $stmt_delete_image->bind_param("i", $image_id);
                $stmt_delete_image->execute();
                $stmt_delete_image->close();
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
    <div class="color-plate ">
        <a class="color-plate-icon"><i class="ti-paint-bucket"></i></a>
        <h4>Eshop Colors</h4>
        <p>Here is some awesome color's available on Eshop Template.</p>
        <span class="color1"></span>
        <span class="color2"></span>
        <span class="color3"></span>
        <span class="color4"></span>
        <span class="color5"></span>
        <span class="color6"></span>
        <span class="color7"></span>
        <span class="color8"></span>
        <span class="color9"></span>
        <span class="color10"></span>
        <span class="color11"></span>
        <span class="color12"></span>
    </div>
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
                            <li><a href="manage_products.php">Manage Products<i class="ti-arrow-right"></i></a></li>
                            <li class="active">Edit Product</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <div class="container pt-3">
        <div class="form-main">
            <!-- <h1>Edit Product</h1> -->
            <form action="edit_product.php?id=<?php echo $product_id; ?>" method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input class="form-control rounded px-3 py-2" placeholder="Name" type="text" name="name" id="name" value="<?php echo $product['name']; ?>" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <textarea class="form-control rounded px-3 py-2" placeholder="Description" name="description" id="description" required><?php echo $product['description']; ?></textarea>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input class="form-control rounded px-3 py-2" placeholder="Price" type="text" name="price" id="price" value="<?php echo $product['price']; ?>" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input class="form-control rounded px-3 py-2" placeholder="Discount Price" type="text" name="discount_price" id="discount_price" value="<?php echo $product['discount_price']; ?>">
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input class="form-control rounded px-3 py-2" type="datetime-local" name="discount_end_date" id="discount_end_date" value="<?php echo date('Y-m-d\TH:i', strtotime($product['discount_end_date'])); ?>">
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <select class="form-control rounded px-3 py-2" name="category_id" id="category_id" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] == $product['category_id']) echo 'selected'; ?>><?php echo $category['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input class="form-control rounded px-3 py-2" placeholder="Stock" type="number" name="stock" id="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input class="form-control rounded px-3 py-2" placeholder="SKU" type="text" name="sku" id="sku" value="<?php echo htmlspecialchars($product['sku']); ?>">
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input class="form-control rounded px-3 py-2" placeholder="Weight (kg)" type="text" name="weight" id="weight" value="<?php echo htmlspecialchars($product['weight']); ?>">
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input class="form-control rounded px-3 py-2" placeholder="Dimensions" type="text" name="dimensions" id="dimensions" value="<?php echo htmlspecialchars($product['dimensions']); ?>">
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input class="form-control rounded px-3 py-2" placeholder="Material" type="text" name="material" id="material" value="<?php echo htmlspecialchars($product['material']); ?>">
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input class="form-control rounded px-3 py-2" placeholder="Color" type="text" name="color" id="color" value="<?php echo htmlspecialchars($product['color']); ?>">
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input class="form-control rounded px-3 py-2" placeholder="Brand" type="text" name="brand" id="brand" value="<?php echo htmlspecialchars($product['brand']); ?>">
                        </div>
                    </div>
                    <div class="col-lg-12 col-12">
                        <div class="form-group">
                            <label for="images">Product Images</label>
                            <input class="px-3 py-2" type="file" name="images[]" id="images" multiple>
                        </div>
                    </div>

                    <div class="col-lg-12 col-12">
                        <div class="form-group">
                            <label>Existing Images:</label><br>
                            <?php
                            $product_images = [];
                            $stmt_images = $conn->prepare("SELECT id, image_path FROM product_images WHERE product_id = ?");
                            $stmt_images->bind_param("i", $product_id);
                            $stmt_images->execute();
                            $result_images = $stmt_images->get_result();
                            while ($row = $result_images->fetch_assoc()) {
                                $product_images[] = $row;
                            }
                            $stmt_images->close();

                            if (!empty($product_images)):
                                foreach ($product_images as $image): ?>
                                    <div class="d-inline-block me-2 mb-2">
                                        <img src="assets/images/<?php echo htmlspecialchars($image['image_path']); ?>" alt="Product Image" width="100">
                                        <input type="checkbox" name="delete_images[]" value="<?php echo $image['id']; ?>"> Delete
                                    </div>
                                <?php endforeach;
                            else: ?>
                                <p>No images uploaded for this product.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary rounded m-3 text-white">Update Product</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
