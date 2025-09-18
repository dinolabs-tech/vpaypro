<?php
// models/online_store.php

class OnlineStore {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getCartItems($customer_id) {
        $cartItems = [];
        if (isset($_SESSION['online_cart']) && !empty($_SESSION['online_cart'])) {
            foreach ($_SESSION['online_cart'] as $item) {
                $cartItems[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'price' => $item['price'],
                    'quantity' => $item['qty'],
                    'branch_id' => $item['branch_id']
                ];
            }
        }
        return $cartItems;
    }

    public function getCustomerBalance($customer_id) {
        $stmt = $this->conn->prepare("SELECT balance FROM customers WHERE id = ?");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return (float)$row['balance'];
        }
        $stmt->close();
        return 0.0;
    }

    public function getProducts($country = null, $state = null) {
        $query = "
            SELECT p.*, SUM(bpi.quantity) AS total_quantity
            FROM product p
            LEFT JOIN branch_product_inventory bpi ON p.productid = bpi.productid
        ";
        $queryParams = [];
        $queryTypes = "";

        if ($country && $state) {
            $query .= " JOIN branches br ON bpi.branch_id = br.branch_id WHERE br.country = ? AND br.state = ?";
            $queryTypes .= "ss";
            $queryParams[] = $country;
            $queryParams[] = $state;
        } elseif ($country) {
            $query .= " JOIN branches br ON bpi.branch_id = br.branch_id WHERE br.country = ?";
            $queryTypes .= "s";
            $queryParams[] = $country;
        }

        $query .= " GROUP BY p.productid ORDER BY p.productname ASC";

        if (!empty($queryParams)) {
            $stmt = $this->conn->prepare($query);
            if ($stmt === false) {
                throw new Exception("Prepare failed: " . $this->conn->error);
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
            $result = $this->conn->query($query);
        }

        $products = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        } else {
            throw new Exception("Error fetching products: " . $this->conn->error);
        }
        return $products;
    }
}
?>