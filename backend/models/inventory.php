<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include './database/db_connection.php';
session_start();


// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch branch_id, country, state, and role for the logged-in user
$user_branch_id = null;
$user_country = $_SESSION['country'] ?? null; // Use session variable if available
$user_state = $_SESSION['state'] ?? null; // Use session variable if available
$user_role = $_SESSION['role'] ?? null;
$user_branch_id = $_SESSION['branch_id'] ?? NULL;

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT l.branch_id, l.country, l.state, l.role FROM login l LEFT JOIN branches b ON l.branch_id = b.branch_id WHERE l.id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    if ($user_data) {
        $user_branch_id = $user_data['branch_id'];
        $user_country = $user_data['country'] ?? ''; // Ensure it's a string
        $user_state = $user_data['state'] ?? '';   // Ensure it's a string
        $user_role = $user_data['role']; // Ensure role is up-to-date from DB
    }
    $stmt->close();
}

// Initialize product array
$products = [];

// Fetch all products with total quantity from branch_product_inventory
$productQuery = "
    SELECT p.*, SUM(bpi.quantity) AS total_quantity
    FROM product p
    LEFT JOIN branch_product_inventory bpi ON p.productid = bpi.productid
";

$queryParams = [];
$queryTypes = "";

if ($user_role !== 'Superuser' && $user_role !== 'CEO') {
    $productQuery .= " JOIN branches br ON bpi.branch_id = br.branch_id WHERE 1=1";
    if ($user_country !== null) {
        $productQuery .= " AND br.country = ?";
        $queryTypes .= "s";
        $queryParams[] = $user_country;
    }
    if ($user_state !== null) {
        $productQuery .= " AND br.state = ?";
        $queryTypes .= "s";
        $queryParams[] = $user_state;
    }
    if ($user_branch_id !== null) {
        $productQuery .= " AND br.branch_id = ?";
        $queryTypes .= "s";
        $queryParams[] = $user_branch_id;
    }
}

$productQuery .= " GROUP BY p.productid ORDER BY p.productid DESC";

// Prepare and execute the statement
if (!empty($queryParams)) {
    $stmt = $conn->prepare($productQuery);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $bind_params = [];
    $bind_params[] = $queryTypes;
    foreach ($queryParams as $key => $value) {
        $bind_params[] = &$queryParams[$key]; // Pass by reference
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_params);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $result = $conn->query($productQuery);
}

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    die("Error fetching products: " . $conn->error);
}

// Fetch all categories
$categories = [];
$categoryQuery = "SELECT * FROM categories ORDER BY name ASC";
$result = $conn->query($categoryQuery);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
} else {
    die("Error fetching categories: " . $conn->error);
}

// =============== EDIT MODE ==========================
$editMode = false;
$productToEdit = [
    'productid' => '',
    'productname' => '',
    'sku' => '', // Add SKU here
    'unitprice' => '',
    'sellprice' => '',
    'description' => '',
    'reorder_level' => '',
    'reorder_qty' => '',
    'branch_id' => '', // Add branch_id to productToEdit
    'initial_quantity' => '' // Add initial_quantity
];

if (isset($_GET['id'])) {
    $editId = $_GET['id'];
    $stmt = $conn->prepare("SELECT p.*, bpi.branch_id, bpi.quantity AS initial_quantity 
                            FROM product p
                            LEFT JOIN branch_product_inventory bpi ON p.productid = bpi.productid
                            WHERE p.productid = ?"); // Fetch branch_id and quantity
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $productToEdit = $result->fetch_assoc();
        $editMode = true;

        // Fetch product categories
        $productToEdit['categories'] = [];
        $stmt = $conn->prepare("SELECT category_id FROM product_categories WHERE product_id = ?");
        $stmt->bind_param("i", $editId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $productToEdit['categories'][] = $row['category_id'];
        }

        // Fetch product variations
        $productToEdit['variations'] = [];
        $stmt = $conn->prepare("SELECT * FROM product_variations WHERE product_id = ?");
        $stmt->bind_param("i", $editId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $productToEdit['variations'][] = $row;
        }
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = trim($_POST['productname'] ?? '');
    $sku = trim($_POST['sku'] ?? ''); // Retrieve SKU
    $unitPrice = filter_var($_POST['unitprice'] ?? 0, FILTER_VALIDATE_FLOAT);
    $sellPrice = filter_var($_POST['sellprice'] ?? 0, FILTER_VALIDATE_FLOAT);
    $initialQuantity = filter_var($_POST['initial_quantity'] ?? 0, FILTER_VALIDATE_INT);
    $branchId = filter_var($_POST['branch_id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
    $description = trim($_POST['description'] ?? '');
    $reorderLevel = filter_var($_POST['reorder_level'] ?? 0, FILTER_VALIDATE_INT);
    $reorderQty = filter_var($_POST['reorder_qty'] ?? 0, FILTER_VALIDATE_INT);

    // Validate inputs
    $errors = [];
    if ($productName === '') $errors[] = 'Product name is required.';
    if ($sku === '') $errors[] = 'SKU is required.'; // Validate SKU
    if ($unitPrice === false || $unitPrice < 0) $errors[] = 'Unit price must be non-negative.';
    if ($sellPrice === false || $sellPrice < 0) $errors[] = 'Sell price must be non-negative.';
    if ($initialQuantity === false || $initialQuantity < 0) $errors[] = 'Initial Quantity must be non-negative.';
    if ($branchId === false || $branchId <= 0) $errors[] = 'Branch is required.';
    if ($reorderLevel === false || $reorderLevel < 0) $errors[] = 'Reorder level must be non-negative.';
    if ($reorderQty === false || $reorderQty < 0) $errors[] = 'Reorder quantity must be non-negative.';

    if (!empty($errors)) {
        foreach ($errors as $err) {
            echo "<p style='color:red;'>Error: {$err}</p>";
        }
        exit;
    }

    // Image upload handling
    $oldImagePath = null;
    if (isset($_POST['edit_id']) && is_numeric($_POST['edit_id'])) {
        $editId = $_POST['edit_id'];
        $stmt_old_img = $conn->prepare("SELECT image_url FROM product WHERE productid = ?");
        if ($stmt_old_img === false) {
            // Log error and potentially set a default or handle gracefully
            error_log("Prepare failed for old image fetch: " . $conn->error);
            $oldImagePath = null; // Ensure it's null if prepare fails
        } else {
            $stmt_old_img->bind_param("i", $editId);
            $stmt_old_img->execute();
            $result_old_img = $stmt_old_img->get_result();
            if ($result_old_img && $result_old_img->num_rows > 0) {
                $row_old_img = $result_old_img->fetch_assoc();
                $oldImagePath = $row_old_img['image_url'];
            }
            $stmt_old_img->close();
        }
    }

    $imagePath = null; // Initialize $imagePath
    $uploadDir = 'assets/img/products/';

    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            echo "<p style='color:red;'>Error: Could not create upload directory.</p>";
            // If directory creation fails, we cannot upload new images.
            // If it's an edit, we might still want to keep the old image.
            // If it's a new product, we'll have to use a default or fail.
        }
    }

    if (!empty($_FILES['product_image']['name'])) {
        // File was uploaded
        $fileExtension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $fileExtension;
        $uploadFilePath = $uploadDir . $newFileName;

        // Delete old image if it exists and is not the default
        if (!empty($oldImagePath) && $oldImagePath !== 'assets/img/products/default.jpg' && file_exists($oldImagePath)) {
            if (!unlink($oldImagePath)) {
                // Log or display an error if deletion fails, but continue with upload
                error_log("Failed to delete old image: " . $oldImagePath);
            }
        }

        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadFilePath)) {
            $imagePath = $uploadFilePath;
        } else {
            echo "<p style='color:red;'>Error uploading image.</p>";
            // If upload fails, use the old image path if available, otherwise default
            $imagePath = !empty($oldImagePath) ? $oldImagePath : 'assets/img/products/default.jpg';
        }
    } else {
        // No file was uploaded, keep the existing image or use default
        $imagePath = !empty($oldImagePath) ? $oldImagePath : 'assets/img/products/default.jpg';
    }

    // Prepare SQL statement and bind parameters
    if ($imagePath !== null) {
        // If we have an image path (either uploaded or default/existing)
        if (isset($_POST['edit_id']) && is_numeric($_POST['edit_id'])) {
            // Update existing product with image
            $editId = $_POST['edit_id'];
            $sql = "UPDATE product SET productname=?, sku=?, unitprice=?, sellprice=?, description=?, reorder_level=?, reorder_qty=?, country=?, state=?, image_url=? WHERE productid=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssddsiisssi", $productName, $sku, $unitPrice, $sellPrice, $description, $reorderLevel, $reorderQty, $user_country, $user_state, $imagePath, $editId);
            $product_id = $editId;
        } else {
            // Insert new product with image
            $sql = "INSERT INTO product (productname, sku, unitprice, sellprice, description, reorder_level, reorder_qty, country, state, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssddsiisss", $productName, $sku, $unitPrice, $sellPrice, $description, $reorderLevel, $reorderQty, $user_country, $user_state, $imagePath);
        }
    } else {
        // If $imagePath is null (e.g., directory creation failed and no existing image)
        // we should not include image_url in the query.
        if (isset($_POST['edit_id']) && is_numeric($_POST['edit_id'])) {
            // Update existing product without image_url
            $editId = $_POST['edit_id'];
            $sql = "UPDATE product SET productname=?, sku=?, unitprice=?, sellprice=?, description=?, reorder_level=?, reorder_qty=?, country=?, state=? WHERE productid=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssddsiissi", $productName, $sku, $unitPrice, $sellPrice, $description, $reorderLevel, $reorderQty, $user_country, $user_state, $editId);
            $product_id = $editId;
        } else {
            // Insert new product without image_url
            $sql = "INSERT INTO product (productname, sku, unitprice, sellprice, description, reorder_level, reorder_qty, country, state) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssddsiiss", $productName, $sku, $unitPrice, $sellPrice, $description, $reorderLevel, $reorderQty, $user_country, $user_state);
        }
    }

    // Execute the statement and handle subsequent logic

    if ($stmt->execute()) {
        if (!isset($product_id)) {
            $product_id = $stmt->insert_id;
        }

        // Handle branch_product_inventory
        $sql_bpi = "INSERT INTO branch_product_inventory (branch_id, productid, quantity) 
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)";
        $stmt_bpi = $conn->prepare($sql_bpi);
        $stmt_bpi->bind_param("iii", $branchId, $product_id, $initialQuantity);
        $stmt_bpi->execute();
        $stmt_bpi->close();

        // Handle categories
        $selected_categories = $_POST['categories'] ?? [];

        // First, remove existing categories for this product
        $stmt = $conn->prepare("DELETE FROM product_categories WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->close();

        // Then, insert the new categories
        if (!empty($selected_categories)) {
            $stmt = $conn->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)");
            foreach ($selected_categories as $category_id) {
                $stmt->bind_param("ii", $product_id, $category_id);
                $stmt->execute();
            }
            $stmt->close();
        }

        // Handle product variations
        $variation_names = $_POST['variation_name'] ?? [];
        $variation_values = $_POST['variation_value'] ?? [];
        $price_modifiers = $_POST['price_modifier'] ?? [];
        $skus = $_POST['sku'] ?? [];
        $stock_levels = $_POST['stock_level'] ?? [];
        $variation_ids = $_POST['variation_id'] ?? [];

        // Delete existing variations not in the current submission
        if (isset($editId)) {
            $existing_variation_ids = [];
            $stmt = $conn->prepare("SELECT id FROM product_variations WHERE product_id = ?");
            $stmt->bind_param("i", $editId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $existing_variation_ids[] = $row['id'];
            }
            $stmt->close();

            $variations_to_delete = array_diff($existing_variation_ids, $variation_ids);
            if (!empty($variations_to_delete)) {
                $placeholders = implode(',', array_fill(0, count($variations_to_delete), '?'));
                $stmt = $conn->prepare("DELETE FROM product_variations WHERE id IN ($placeholders) AND product_id = ?");
                $types = str_repeat('i', count($variations_to_delete)) . 'i';
                $params = array_merge($variations_to_delete, [$editId]);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $stmt->close();
            }
        }

        // Insert or update variations
        for ($i = 0; $i < count($variation_names); $i++) {
            $var_name = trim($variation_names[$i]);
            $var_value = trim($variation_values[$i]);
            $price_mod = filter_var($price_modifiers[$i], FILTER_VALIDATE_FLOAT);
            $sku = trim($skus[$i]);
            $stock_lvl = filter_var($stock_levels[$i], FILTER_VALIDATE_INT);
            $var_id = filter_var($variation_ids[$i], FILTER_VALIDATE_INT);

            if ($var_name === '' || $var_value === '') {
                continue; // Skip if name or value is empty
            }

            if ($var_id > 0 && isset($editId)) {
                // Update existing variation
                $sql = "UPDATE product_variations SET variation_name=?, variation_value=?, price_modifier=?, sku=?, stock_level=? WHERE id=? AND product_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdsiii", $var_name, $var_value, $price_mod, $sku, $stock_lvl, $var_id, $product_id);
            } else {
                // Insert new variation
                $sql = "INSERT INTO product_variations (product_id, variation_name, variation_value, price_modifier, sku, stock_level) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("issdsi", $product_id, $var_name, $var_value, $price_mod, $sku, $stock_lvl);
            }
            $stmt->execute();
            $stmt->close();
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "<p style='color:red;'>Error saving product: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
