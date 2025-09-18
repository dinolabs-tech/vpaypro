<?php
// Enable reporting of all PHP errors for debugging purposes
error_reporting(E_ALL);
// Display errors directly in the browser (development only)
ini_set('display_errors', 1);

// Start or resume the current session to track user data across requests

include './database/db_connection.php';
session_start();

// If the user is not logged in (no user_id in session), redirect them to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Send HTTP header to redirect
    exit(); // Stop further script execution after redirect
}

// If the connection to the database failed, terminate with an error message
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Prepare and execute a query to fetch transactions
$productQuery = "SELECT td.*, l.staffname, b.branch_name AS branch FROM transactiondetails td JOIN login l ON td.cashier = l.id";

$queryParams = [];
$queryTypes = "";

if (isset($_SESSION['role']) && $_SESSION['role'] !== 'Superuser' && $_SESSION['role'] !== 'CEO') {
    $productQuery .= " JOIN branches b ON l.branch_id = b.branch_id WHERE 1=1";
    if (isset($_SESSION['country'])) {
        $productQuery .= " AND b.country = ?";
        $queryTypes .= "s";
        $queryParams[] = $_SESSION['country'];
    }
    if (isset($_SESSION['state'])) {
        $productQuery .= " AND b.state = ?";
        $queryTypes .= "s";
        $queryParams[] = $_SESSION['state'];
    }
} else {
    // If Superuser/CEO, still join branches to get branch name, but no filtering
    $productQuery .= " LEFT JOIN branches b ON l.branch_id = b.branch_id";
}

$productQuery .= " ORDER BY td.transactionID DESC";

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
    // Loop through each row in the result set and add it to $products
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    // If the query failed, stop execution and display the error
    die("Error fetching products: " . $conn->error);
}



// Close the database connection to free resources
$conn->close();
?>
