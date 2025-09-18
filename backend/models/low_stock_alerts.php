<?php
// Include database connection
require_once './database/db_connection.php';

// Function to get low stock products
function getLowStockProducts($conn, $user_role, $user_country, $user_state)
{
    $lowStockProducts = [];
    $sql = "SELECT p.productid, p.productname, p.reorder_level, p.reorder_qty, bpi.quantity, b.branch_name FROM product p LEFT JOIN branch_product_inventory bpi ON p.productid = bpi.productid INNER JOIN branches b ON bpi.branch_id = b.branch_id WHERE bpi.quantity <= p.reorder_level";

    $queryParams = [];
    $queryTypes = "";

    if ($user_role !== 'Superuser' && $user_role !== 'CEO') {
        if ($user_country !== null) {
            $sql .= " AND b.country = ?";
            $queryTypes .= "s";
            $queryParams[] = $user_country;
        }
        if ($user_state !== null) {
            $sql .= " AND b.state = ?";
            $queryTypes .= "s";
            $queryParams[] = $user_state;
        }
    }

    $sql .= " ORDER BY p.productname ASC";

    if (!empty($queryParams)) {
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            error_log("Prepare failed for low stock products: " . $conn->error . " for SQL: " . $sql);
            return [];
        }
        $bind_params = [];
        $bind_params[] = $queryTypes;
        foreach ($queryParams as $key => $value) {
            $bind_params[] = &$queryParams[$key];
        }
        call_user_func_array([$stmt, 'bind_param'], $bind_params);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } else {
        $result = $conn->query($sql);
    }

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $lowStockProducts[] = $row;
        }
    } else {
        error_log("Error fetching low stock products: " . $conn->error);
    }
    return $lowStockProducts;
}

// This file will be included, so we don't close the connection here.
// The connection will be closed by the including script.
