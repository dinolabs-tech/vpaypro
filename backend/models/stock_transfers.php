<?php
// Include database connection
require_once './database/db_connection.php';

class StockTransfer {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Create a new stock transfer
    // Create a new stock transfer (product to product - old method, kept for reference/backward compatibility if needed)
    public function createStockTransfer($from_product_id, $to_product_id, $quantity, $notes) {
        // This method is for product-to-product transfers, not branch-to-branch.
        // It might need to be adapted or removed depending on the final system design.
        // For now, it's left as is, but the new branch transfer method will be used.
        $this->conn->begin_transaction();
        try {
            // Decrease quantity from the 'from' product
            $sql_decrease = "UPDATE product SET qty = qty - ? WHERE productid = ?";
            $stmt_decrease = $this->conn->prepare($sql_decrease);
            $stmt_decrease->bind_param("ii", $quantity, $from_product_id);
            $stmt_decrease->execute();
            $stmt_decrease->close();

            // Increase quantity for the 'to' product
            $sql_increase = "UPDATE product SET qty = qty + ? WHERE productid = ?";
            $stmt_increase = $this->conn->prepare($sql_increase);
            $stmt_increase->bind_param("ii", $quantity, $to_product_id);
            $stmt_increase->execute();
            $stmt_increase->close();

            // Record the stock transfer
            $sql_transfer = "INSERT INTO stock_transfers (from_product_id, to_product_id, quantity, notes) VALUES (?, ?, ?, ?)";
            $stmt_transfer = $this->conn->prepare($sql_transfer);
            $stmt_transfer->bind_param("iiis", $from_product_id, $to_product_id, $quantity, $notes);
            $stmt_transfer->execute();
            $transfer_id = $stmt_transfer->insert_id;
            $stmt_transfer->close();

            $this->conn->commit();
            return $transfer_id;
        } catch (mysqli_sql_exception $exception) {
            $this->conn->rollback();
            error_log("Error creating stock transfer: " . $exception->getMessage());
            return false;
        }
    }

    // Get all product-to-product stock transfers (old method, kept for reference/backward compatibility if needed)
    public function getAllProductToProductTransfers() {
        $transfers = [];
        $sql = "SELECT st.*, p_from.productname AS from_product_name, p_to.productname AS to_product_name 
                FROM stock_transfers st
                JOIN product p_from ON st.from_product_id = p_from.productid
                JOIN product p_to ON st.to_product_id = p_to.productid
                ORDER BY st.transfer_date DESC";
        $result = $this->conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $transfers[] = $row;
            }
        } else {
            error_log("Error fetching product-to-product stock transfers: " . $this->conn->error);
        }
        return $transfers;
    }

    // Get a single product-to-product stock transfer by ID (old method, kept for reference/backward compatibility if needed)
    public function getProductToProductTransferById($id) {
        $transfer = null;
        $sql = "SELECT st.*, p_from.productname AS from_product_name, p_to.productname AS to_product_name 
                FROM stock_transfers st
                JOIN product p_from ON st.from_product_id = p_from.productid
                JOIN product p_to ON st.to_product_id = p_to.productid
                WHERE st.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $transfer = $result->fetch_assoc();
        }
        $stmt->close();
        return $transfer;
    }

    // New method: Get all branch-to-branch stock transfers
    public function getAllBranchToBranchTransfers() {
        $transfers = [];
        $sql = "SELECT st.*, 
                       b_from.branch_name AS from_branch_name, 
                       b_to.branch_name AS to_branch_name,
                       p.productname AS product_name
                FROM stock_transfers st
                JOIN branches b_from ON st.from_branch_id = b_from.branch_id
                JOIN branches b_to ON st.to_branch_id = b_to.branch_id
                JOIN product p ON st.product_id = p.productid
                ORDER BY st.transfer_date DESC";
        $result = $this->conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $transfers[] = $row;
            }
        } else {
            error_log("Error fetching branch-to-branch stock transfers: " . $this->conn->error);
        }
        return $transfers;
    }

    // New method: Get a single branch-to-branch stock transfer by ID
    public function getBranchToBranchTransferById($id) {
        $transfer = null;
        $sql = "SELECT st.*, 
                       b_from.branch_name AS from_branch_name, 
                       b_to.branch_name AS to_branch_name,
                       p.productname AS product_name
                FROM stock_transfers st
                JOIN branches b_from ON st.from_branch_id = b_from.branch_id
                JOIN branches b_to ON st.to_branch_id = b_to.branch_id
                JOIN product p ON st.product_id = p.productid
                WHERE st.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $transfer = $result->fetch_assoc();
        }
        $stmt->close();
        return $transfer;
    }

    // New method: Get products by branch
    public function getProductsByBranch($branchId) {
        $products = [];
        $sql = "SELECT p.productid, p.productname, bpi.quantity AS qty 
                FROM product p
                JOIN branch_product_inventory bpi ON p.productid = bpi.productid
                WHERE bpi.branch_id = ? AND bpi.quantity > 0
                ORDER BY p.productname ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $branchId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        } else {
            error_log("Error fetching products by branch: " . $this->conn->error);
        }
        $stmt->close();
        return $products;
    }

    // New method: Get product stock in a specific branch
    public function getProductStockInBranch($productId, $branchId) {
        $quantity = 0;
        $sql = "SELECT quantity FROM branch_product_inventory WHERE productid = ? AND branch_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $productId, $branchId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $quantity = $row['quantity'];
        }
        $stmt->close();
        return $quantity;
    }

    // New method: Create a new branch-to-branch stock transfer
    public function createBranchStockTransfer($fromBranchId, $toBranchId, $productId, $quantity, $notes) {
        $this->conn->begin_transaction();
        try {
            // Decrease quantity from the source branch
            $sql_decrease = "UPDATE branch_product_inventory SET quantity = quantity - ? WHERE productid = ? AND branch_id = ?";
            $stmt_decrease = $this->conn->prepare($sql_decrease);
            $stmt_decrease->bind_param("iii", $quantity, $productId, $fromBranchId);
            $stmt_decrease->execute();
            $stmt_decrease->close();

            // Increase quantity for the destination branch (UPSERT: insert if not exists, update if exists)
            $sql_increase = "INSERT INTO branch_product_inventory (branch_id, productid, quantity) 
                             VALUES (?, ?, ?)
                             ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
            $stmt_increase = $this->conn->prepare($sql_increase);
            $stmt_increase->bind_param("iii", $toBranchId, $productId, $quantity);
            $stmt_increase->execute();
            $stmt_increase->close();

            // Record the stock transfer in the stock_transfers table
            // Note: The stock_transfers table currently uses from_product_id and to_product_id.
            // We need to decide if we want to update this table to use branch_ids or create a new table.
            // For now, I'll record it using product_id and add branch_ids as notes or new columns if schema changes.
            // For a proper solution, the stock_transfers table schema should be updated to include branch_ids.
            // Assuming stock_transfers table will be updated to include from_branch_id and to_branch_id
            $sql_transfer = "INSERT INTO stock_transfers (from_branch_id, to_branch_id, product_id, quantity, notes) VALUES (?, ?, ?, ?, ?)";
            $stmt_transfer = $this->conn->prepare($sql_transfer);
            $stmt_transfer->bind_param("iiiis", $fromBranchId, $toBranchId, $productId, $quantity, $notes);
            $stmt_transfer->execute();
            $transfer_id = $stmt_transfer->insert_id;
            $stmt_transfer->close();

            $this->conn->commit();
            return $transfer_id;
        } catch (mysqli_sql_exception $exception) {
            $this->conn->rollback();
            error_log("Error creating branch stock transfer: " . $exception->getMessage());
            return false;
        }
    }
}

// This file will be included, so we don't close the connection here.
// The connection will be closed by the including script.
?>
