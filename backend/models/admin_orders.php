<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include './database/db_connection.php';
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch branch_id, country, state, and role for the logged-in user
$user_branch_id = null;
$user_country = null;
$user_state = null;
$user_role = $_SESSION['role'] ?? null;

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT l.branch_id, b.country, b.state, l.role FROM login l LEFT JOIN branches b ON l.branch_id = b.branch_id WHERE l.id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    if ($user_data) {
        $user_branch_id = $user_data['branch_id'];
        $user_country = $_SESSION['country'];
        $user_state = $_SESSION['state'];
        $user_role = $user_data['role']; // Ensure role is up-to-date from DB
    }
    $stmt->close();
}

$orders = [];
$sql = "SELECT o.id, c.name, o.customer_id, o.total_amount, o.status, o.order_date FROM orders o 
INNER JOIN customers c ON o.customer_id = c.customer_id"; // Added country, state to select

$queryParams = [];
$queryTypes = "";

if ($user_role !== 'Superuser' && $user_role !== 'CEO') {
    $sql .= " WHERE 1=1";
    if ($user_country !== null) {
        $sql .= " AND country = ?";
        $queryTypes .= "s";
        $queryParams[] = $user_country;
    }
    if ($user_state !== null) {
        $sql .= " AND state = ?";
        $queryTypes .= "s";
        $queryParams[] = $user_state;
    }
}

$sql .= " ORDER BY order_date DESC";

if (!empty($queryParams)) {
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $bind_params = [];
    $bind_params[] = $queryTypes;
    for ($i = 1; $i < count($queryParams) + 1; $i++) {
        $bind_params[] = &$queryParams[$i-1];
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_params);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $result = $conn->query($sql);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
?>
