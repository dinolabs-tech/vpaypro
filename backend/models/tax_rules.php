<?php
// models/tax_rules.php


include './database/db_connection.php';
session_start();

class TaxRule {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Method to get all tax rules
    public function getAllTaxRules() {
        $sql = "SELECT * FROM tax_rules";
        $result = $this->conn->query($sql);
        return $result;
    }

    // Method to get a single tax rule by ID
    public function getTaxRuleById($taxRuleId) {
        $sql = "SELECT * FROM tax_rules WHERE tax_rule_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $taxRuleId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Method to create a new tax rule
    public function createTaxRule($ruleName, $taxRateId, $appliesToProductType = null, $appliesToOrderTotal = null, $isActive = true) {
        $sql = "INSERT INTO tax_rules (rule_name, tax_rate_id, applies_to_product_type, applies_to_order_total, is_active) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("siis", $ruleName, $taxRateId, $appliesToProductType, $appliesToOrderTotal, $isActive);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Tax Rule Creation Error: " . $this->conn->error);
            return false;
        }
    }

    // Method to update an existing tax rule
    public function updateTaxRule($taxRuleId, $ruleName, $taxRateId, $appliesToProductType, $appliesToOrderTotal, $isActive) {
        $sql = "UPDATE tax_rules SET rule_name = ?, tax_rate_id = ?, applies_to_product_type = ?, applies_to_order_total = ?, is_active = ? WHERE tax_rule_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("siisi", $ruleName, $taxRateId, $appliesToProductType, $appliesToOrderTotal, $isActive, $taxRuleId);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Tax Rule Update Error: " . $this->conn->error);
            return false;
        }
    }

    // Method to delete a tax rule
    public function deleteTaxRule($taxRuleId) {
        $sql = "DELETE FROM tax_rules WHERE tax_rule_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $taxRuleId);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Tax Rule Deletion Error: " . $this->conn->error);
            return false;
        }
    }
}
?>
