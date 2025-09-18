<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Initialize product array
$suppliers = [];

// Fetch all products
$productQuery = "SELECT * FROM suppliers";
$result = $conn->query($productQuery);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $suppliers[] = $row;
    }
} else {
    die("Error fetching Suppliers: " . $conn->error);
}

// =============== EDIT MODE ==========================
$editMode = false;
$productToEdit = [
    'id' => '',
    'name' => '',
    'product' => '',
    'companyname' => '',
    'phone' => '',
    'email' => '',
    'address' => '',
    'password'=>''
];

if (isset($_GET['id'])) {
    $editId = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM suppliers WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $productToEdit = $result->fetch_assoc();
        $editMode = true;
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $productName = trim($_POST['product'] ?? '');
    $companyname = trim($_POST['companyname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Validate inputs
    $errors = [];
    if ($name === '') $errors[] = 'Supplier`s Name is required.';
    if ($productName === '') $errors[] = 'Product name is required.';
    if ($companyname === '') $errors[] = 'Business Name is required.';
    if ($phone === '') $errors[] = 'Supplier`s Mobile is required.';
    if ($email === '') $errors[] = 'Business Email is Required.';
    if ($address === '') $errors[] = 'Address is required.';

    if (!empty($errors)) {
        foreach ($errors as $err) {
            echo "<p style='color:red;'>Error: {$err}</p>";
        }
        exit;
    }


    // Check if it's an edit
    if (isset($_POST['edit_id']) && is_numeric($_POST['edit_id'])) {
        $editId = $_POST['edit_id'];
        $sql = "UPDATE suppliers SET name=?, product=?, companyname=?, phone=?, email=?, address=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $name, $productName, $companyname, $phone, $email, $address, $editId);
    } else {
        $sql = "INSERT INTO suppliers (name, product, companyname, phone, email, address, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $name, $productName, $companyname, $phone, $email, $address, $password);
    }

    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "<p style='color:red;'>Error saving Supplier: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
