<?php
// models/tax_rates.php

include_once __DIR__ . '/../database/db_connection.php'; // Ensure db connection is included

class TaxRate {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Method to get all tax rates
    public function getAllTaxRates() {
        $sql = "SELECT * FROM tax_rates";
        $result = $this->conn->query($sql);
        return $result;
    }

    // Method to get a single tax rate by ID
    public function getTaxRateById($taxRateId) {
        $sql = "SELECT * FROM tax_rates WHERE tax_rate_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $taxRateId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Method to create a new tax rate
    public function createTaxRate($country, $state, $taxRate, $isActive = true) {
        $sql = "INSERT INTO tax_rates (country, state, tax_rate, is_active) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", $country, $state, $taxRate, $isActive);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Tax Rate Creation Error: " . $this->conn->error);
            return false;
        }
    }

    // Method to update an existing tax rate
    public function updateTaxRate($taxRateId, $country, $state, $taxRate, $isActive) {
        $sql = "UPDATE tax_rates SET country = ?, state = ?, tax_rate = ?, is_active = ? WHERE tax_rate_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $country, $state, $taxRate, $isActive, $taxRateId);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Tax Rate Update Error: " . $this->conn->error);
            return false;
        }
    }

    // Method to delete a tax rate
    public function deleteTaxRate($taxRateId) {
        $sql = "DELETE FROM tax_rates WHERE tax_rate_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $taxRateId);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Tax Rate Deletion Error: " . $this->conn->error);
            return false;
        }
    }

    // Method to get tax rate by country and state
    public function getTaxRateByLocation($country, $state = null) {
        // Prioritize state-specific rate if provided, otherwise use country-wide rate
        $sql = "SELECT tax_rate FROM tax_rates WHERE country = ? AND state = ? AND is_active = 1 ORDER BY state DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $country, $state);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['tax_rate'];
        } else {
            // If no state-specific rate found, try country-wide rate
            // $sql = "SELECT tax_rate FROM tax_rates WHERE country = ? AND state IS NOT NULL AND is_active = 1 ORDER BY tax_rate_id DESC LIMIT 1";
            $sql = "SELECT tax_rate FROM tax_rates WHERE country = ? AND is_active = 1 ORDER BY tax_rate_id DESC LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $country);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row['tax_rate'];
            } else {
                return 0.00; // Default to 0 tax if no rate is found
            }
        }
    }
}
?>
