<?php
// models/audit_logs.php

include './database/db_connection.php';
 // Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


class AuditLog
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Method to log an action
    public function logAction($userId, $action, $details = null)
    {
        $sql = "INSERT INTO audit_logs (user_id, action, details) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $userId, $action, $details); // Assuming details can be null

        if ($stmt->execute()) {
            return true;
        } else {
            // Log the error for debugging
            error_log("Audit Log Error: " . $this->conn->error);
            return false;
        }
    }

    // Method to get all audit logs
    public function getAllLogs()
    {
        $sql = "SELECT al.*, l.staffname FROM audit_logs al
        INNER JOIN login l on al.user_id = l.id
         ORDER BY timestamp ASC";
        $result = $this->conn->query($sql);
        return $result;
    }

    // Method to get logs for a specific user
    public function getLogsByUser($userId)
    {
        $sql = "SELECT al.*, l.staffname FROM audit_logs al 
        INNER JOIN login l on al.user_id = l.id
        WHERE user_id = ? ORDER BY timestamp ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    // Method to get logs within a date range
    public function getLogsByDateRange($startDate, $endDate)
    {
        $sql = "SELECT al.*, l.staffname FROM audit_logs al 
        INNER JOIN login l on al.user_id = l.id
        WHERE DATE(timestamp) BETWEEN ? AND ? ORDER BY timestamp ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    // Method to get logs by a general identifier (username, customer_id, or user ID)
    public function getLogsByIdentifier($identifier)
    {
        $resolvedUserId = null;
        $foundUserId = null; // Declare $foundUserId here

        // 1. Try to find user_id in login table by username
        $stmt_login_username = $this->conn->prepare("SELECT id FROM login WHERE username = ?");
        $stmt_login_username->bind_param("s", $identifier);
        $stmt_login_username->execute();
        $stmt_login_username->store_result();
        if ($stmt_login_username->num_rows > 0) {
            $stmt_login_username->bind_result($foundUserId); // Use declared variable
            $stmt_login_username->fetch();
            $resolvedUserId = $foundUserId;
        }
        $stmt_login_username->close();

        // 2. If not found, try to find user_id in customers table by customer_id
        if ($resolvedUserId === null) {
            $stmt_customer_id = $this->conn->prepare("SELECT id FROM customers WHERE customer_id = ?");
            $stmt_customer_id->bind_param("s", $identifier);
            $stmt_customer_id->execute();
            $stmt_customer_id->store_result();
            if ($stmt_customer_id->num_rows > 0) {
                $stmt_customer_id->bind_result($foundUserId); // Use declared variable
                $stmt_customer_id->fetch();
                $resolvedUserId = $foundUserId;
            }
            $stmt_customer_id->close();
        }

        // 3. If not found, try to find user_id in login table by id (as a number)
        if ($resolvedUserId === null && is_numeric($identifier)) {
            $stmt_login_id = $this->conn->prepare("SELECT id FROM login WHERE id = ?");
            $stmt_login_id->bind_param("i", $identifier);
            $stmt_login_id->execute();
            $stmt_login_id->store_result();
            if ($stmt_login_id->num_rows > 0) {
                $stmt_login_id->bind_result($foundUserId); // Use declared variable
                $stmt_login_id->fetch();
                $resolvedUserId = $foundUserId;
            }
            $stmt_login_id->close();
        }

        // 4. If not found, try to find user_id in customers table by id (as a number)
        if ($resolvedUserId === null && is_numeric($identifier)) {
            $stmt_customer_id_numeric = $this->conn->prepare("SELECT id FROM customers WHERE id = ?");
            $stmt_customer_id_numeric->bind_param("i", $identifier);
            $stmt_customer_id_numeric->execute();
            $stmt_customer_id_numeric->store_result();
            if ($stmt_customer_id_numeric->num_rows > 0) {
                $stmt_customer_id_numeric->bind_result($foundUserId); // Use declared variable
                $stmt_customer_id_numeric->fetch();
                $resolvedUserId = $foundUserId;
            }
            $stmt_customer_id_numeric->close();
        }

        // If a user_id was resolved, get logs for that user
        if ($resolvedUserId !== null) {
            return $this->getLogsByUser($resolvedUserId);
        } else {
            // Return an empty result set if no user was found
            $emptyResult = new stdClass(); // Corrected std::class to stdClass
            $emptyResult->num_rows = 0;
            return $emptyResult;
        }
    }
}
