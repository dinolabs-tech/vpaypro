<?php
// models/payment_gateways.php

include './database/db_connection.php';
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}


class PaymentGateway {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Method to get all payment gateways
    public function getAllGateways() {
        $sql = "SELECT * FROM payment_gateways";
        $result = $this->conn->query($sql);
        return $result;
    }

    // Method to get a single payment gateway by ID
    public function getGatewayById($gatewayId) {
        $sql = "SELECT * FROM payment_gateways WHERE gateway_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $gatewayId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Method to create a new payment gateway
    public function createGateway($name, $apiKey, $apiSecret, $isActive = false) {
        $sql = "INSERT INTO payment_gateways (gateway_name, api_key, api_secret, is_active) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $apiKey, $apiSecret, $isActive);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Payment Gateway Creation Error: " . $this->conn->error);
            return false;
        }
    }

    // Method to update an existing payment gateway
    public function updateGateway($gatewayId, $name, $apiKey, $apiSecret, $isActive) {
        $sql = "UPDATE payment_gateways SET gateway_name = ?, api_key = ?, api_secret = ?, is_active = ? WHERE gateway_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssii", $name, $apiKey, $apiSecret, $isActive, $gatewayId);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Payment Gateway Update Error: " . $this->conn->error);
            return false;
        }
    }

    // Method to delete a payment gateway
    public function deleteGateway($gatewayId) {
        $sql = "DELETE FROM payment_gateways WHERE gateway_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $gatewayId);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Payment Gateway Deletion Error: " . $this->conn->error);
            return false;
        }
    }
}
?>
