<?php
// models/email_templates.php

include './database/db_connection.php';
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

class EmailTemplate {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Method to get all email templates
    public function getAllTemplates() {
        $sql = "SELECT * FROM email_templates";
        $result = $this->conn->query($sql);
        return $result;
    }

    // Method to get a single email template by ID
    public function getTemplateById($templateId) {
        $sql = "SELECT * FROM email_templates WHERE template_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $templateId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Method to create a new email template
    public function createTemplate($name, $subject, $body, $trigger) {
        $sql = "INSERT INTO email_templates (template_name, subject, body, event_trigger) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $subject, $body, $trigger);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Email Template Creation Error: " . $this->conn->error);
            return false;
        }
    }

    // Method to update an existing email template
    public function updateTemplate($templateId, $name, $subject, $body, $trigger) {
        $sql = "UPDATE email_templates SET template_name = ?, subject = ?, body = ?, event_trigger = ? WHERE template_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $subject, $body, $trigger, $templateId);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Email Template Update Error: " . $this->conn->error);
            return false;
        }
    }

    // Method to delete an email template
    public function deleteTemplate($templateId) {
        $sql = "DELETE FROM email_templates WHERE template_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $templateId);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Email Template Deletion Error: " . $this->conn->error);
            return false;
        }
    }
}
?>
