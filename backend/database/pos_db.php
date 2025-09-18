<?php

class PosDB {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function get_products() {
        $products = [];
        $productQuery = "SELECT * FROM product";
        $result = $this->conn->query($productQuery);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        } else {
            throw new Exception("Error fetching products: " . $this->conn->error);
        }
        return $products;
    }

    public function get_product_unitprice($product_id) {
        $stmt = $this->conn->prepare("SELECT unitprice FROM product WHERE productid = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $product = $res->fetch_assoc();
        $stmt->close();
        return $product;
    }

    public function update_product_quantity($product_id, $qty) {
        $updateQuery = "UPDATE product SET qty = qty - ?, total = sellprice * qty WHERE productid = ?";
        $stmt = $this->conn->prepare($updateQuery);
        $stmt->bind_param("ii", $qty, $product_id);
        if ($stmt->execute() === FALSE) {
            throw new Exception("Error updating product quantity: " . $stmt->error);
        }
        $stmt->close();
    }

    public function insert_transaction($product_name, $description, $units, $amount, $profit, $cashier) {
        $insertQuery = "INSERT INTO transactiondetails 
            (productname, description, units, amount, transactiondate, profit, cashier) 
            VALUES (?, ?, ?, ?, NOW(), ?, ?)";
        $stmt = $this->conn->prepare($insertQuery);
        $stmt->bind_param(
            "sssdds",
            $product_name,
            $description,
            $units,
            $amount,
            $profit,
            $cashier
        );
        if ($stmt->execute() === FALSE) {
            throw new Exception("Error inserting transaction: " . $stmt->error);
        }
        $stmt->close();
    }
}

?>
