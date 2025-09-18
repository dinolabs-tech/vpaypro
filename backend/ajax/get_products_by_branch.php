<?php
include('../database/db_connection.php');

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request', 'products' => [], 'total' => 0, 'page' => 1];

// Check if branch_id is set and is a valid integer
if (isset($_GET['branch_id'])) {
    $branchId = $_GET['branch_id'];
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 12; // Number of products per page
    $offset = ($page - 1) * $limit;

    // First, get the total number of products for the branch
    $totalSql = "SELECT COUNT(*) as total FROM product p JOIN branch_product_inventory inv ON p.productid = inv.productid WHERE inv.branch_id = ?";
    $totalStmt = $conn->prepare($totalSql);
    $total = 0;
    if ($totalStmt) {
        $totalStmt->bind_param("i", $branchId);
        $totalStmt->execute();
        $totalResult = $totalStmt->get_result();
        $totalRow = $totalResult->fetch_assoc();
        $total = $totalRow['total'];
        $totalStmt->close();
    }

    $sql = "SELECT p.productid, p.productname, p.sellprice, inv.quantity, p.image_url
            FROM product p
            JOIN branch_product_inventory inv ON p.productid = inv.productid
            WHERE inv.branch_id = ?
            LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("iii", $branchId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $products = [];
            while ($product = $result->fetch_assoc()) {
                $products[] = $product;
            }
            $response = ['status' => 'success', 'message' => 'Products fetched successfully', 'products' => $products, 'total' => $total, 'page' => $page];
        } else {
            $response = ['status' => 'success', 'message' => 'No products found for this branch.', 'products' => [], 'total' => 0, 'page' => $page];
        }
        $stmt->close();
    } else {
        $response = ['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error, 'products' => [], 'total' => 0, 'page' => $page];
    }
}

echo json_encode($response);
$conn->close();
?>
