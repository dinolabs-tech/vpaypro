<?php
// Include database connection
require_once './database/db_connection.php';

class PurchaseOrder {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Create a new purchase order
    public function createPurchaseOrder($supplier_id, $order_date, $expected_delivery_date, $status, $total_amount, $items) {
        $this->conn->begin_transaction();
        try {
            $sql = "INSERT INTO purchase_orders (supplier_id, order_date, expected_delivery_date, status, total_amount) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("isssd", $supplier_id, $order_date, $expected_delivery_date, $status, $total_amount);
            $stmt->execute();
            $purchase_order_id = $stmt->insert_id;
            $stmt->close();

            foreach ($items as $item) {
                $sql_item = "INSERT INTO purchase_order_items (purchase_order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)";
                $stmt_item = $this->conn->prepare($sql_item);
                $stmt_item->bind_param("iiidd", $purchase_order_id, $item['product_id'], $item['quantity'], $item['unit_price'], $item['subtotal']);
                $stmt_item->execute();
                $stmt_item->close();
            }

            $this->conn->commit();
            return $purchase_order_id;
        } catch (mysqli_sql_exception $exception) {
            $this->conn->rollback();
            error_log("Error creating purchase order: " . $exception->getMessage());
            return false;
        }
    }

    // Get all purchase orders
    public function getAllPurchaseOrders() {
        $purchaseOrders = [];
        $sql = "SELECT po.*, s.companyname AS supplier_name FROM purchase_orders po JOIN suppliers s ON po.supplier_id = s.id ORDER BY po.created_at DESC";
        $result = $this->conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $purchaseOrders[] = $row;
            }
        } else {
            error_log("Error fetching purchase orders: " . $this->conn->error);
        }
        return $purchaseOrders;
    }

    // Get a single purchase order by ID
    public function getPurchaseOrderById($id) {
        $purchaseOrder = null;
        $sql = "SELECT po.*, s.companyname AS supplier_name FROM purchase_orders po JOIN suppliers s ON po.supplier_id = s.id WHERE po.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $purchaseOrder = $result->fetch_assoc();
            $purchaseOrder['items'] = $this->getPurchaseOrderItems($id);
        }
        $stmt->close();
        return $purchaseOrder;
    }

    // Get items for a specific purchase order
    public function getPurchaseOrderItems($purchase_order_id) {
        $items = [];
        $sql = "SELECT poi.*, p.productname FROM purchase_order_items poi JOIN product p ON poi.product_id = p.productid WHERE poi.purchase_order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $purchase_order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
        return $items;
    }

    // Update a purchase order
    public function updatePurchaseOrder($id, $supplier_id, $order_date, $expected_delivery_date, $status, $total_amount, $items) {
        $this->conn->begin_transaction();
        try {
            $sql = "UPDATE purchase_orders SET supplier_id=?, order_date=?, expected_delivery_date=?, status=?, total_amount=? WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("isssdi", $supplier_id, $order_date, $expected_delivery_date, $status, $total_amount, $id);
            $stmt->execute();
            $stmt->close();

            // Delete existing items
            $sql_delete_items = "DELETE FROM purchase_order_items WHERE purchase_order_id = ?";
            $stmt_delete = $this->conn->prepare($sql_delete_items);
            $stmt_delete->bind_param("i", $id);
            $stmt_delete->execute();
            $stmt_delete->close();

            // Insert new items
            foreach ($items as $item) {
                $sql_item = "INSERT INTO purchase_order_items (purchase_order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)";
                $stmt_item = $this->conn->prepare($sql_item);
                $stmt_item->bind_param("iiidd", $id, $item['product_id'], $item['quantity'], $item['unit_price'], $item['subtotal']);
                $stmt_item->execute();
                $stmt_item->close();
            }

            $this->conn->commit();
            return true;
        } catch (mysqli_sql_exception $exception) {
            $this->conn->rollback();
            error_log("Error updating purchase order: " . $exception->getMessage());
            return false;
        }
    }

    // Delete a purchase order
    public function deletePurchaseOrder($id) {
        $this->conn->begin_transaction();
        try {
            // Delete associated items first
            $sql_delete_items = "DELETE FROM purchase_order_items WHERE purchase_order_id = ?";
            $stmt_items = $this->conn->prepare($sql_delete_items);
            $stmt_items->bind_param("i", $id);
            $stmt_items->execute();
            $stmt_items->close();

            // Then delete the purchase order
            $sql = "DELETE FROM purchase_orders WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (mysqli_sql_exception $exception) {
            $this->conn->rollback();
            error_log("Error deleting purchase order: " . $exception->getMessage());
            return false;
        }
    }

    // Function to get all suppliers
    public function getAllSuppliers() {
        $suppliers = [];
        $sql = "SELECT id, companyname FROM suppliers ORDER BY companyname ASC";
        $result = $this->conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $suppliers[] = $row;
            }
        } else {
            error_log("Error fetching suppliers: " . $this->conn->error);
        }
        return $suppliers;
    }

    // Function to get all products
    public function getAllProducts() {
        $products = [];
        $sql = "SELECT productid, productname, unitprice FROM product ORDER BY productname ASC";
        $result = $this->conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        } else {
            error_log("Error fetching products: " . $this->conn->error);
        }
        return $products;
    }
}

// This file will be included, so we don't close the connection here.
// The connection will be closed by the including script.
?>
