<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include './database/db_connection.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

class Branch {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Create a new branch
    public function createBranch($branchName, $country, $state) {
        $sql = "INSERT INTO branches (branch_name, country, state) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $branchName, $country, $state);

        if ($stmt->execute()) {
            return $stmt->insert_id;
        } else {
            error_log("Error creating branch: " . $stmt->error);
            return false;
        }
        $stmt->close();
    }

    // Get all branches, optionally filtered by country and state
    public function getAllBranches($country = null, $state = null) {
        $branches = [];
        $sql = "SELECT branch_id, branch_name, country, state FROM branches WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($country)) {
            $sql .= " AND country = ?";
            $params[] = $country;
            $types .= "s";
        }
        if (!empty($state)) {
            $sql .= " AND state = ?";
            $params[] = $state;
            $types .= "s";
        }

        $sql .= " ORDER BY branch_name ASC";

        if (!empty($params)) {
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                error_log("Error preparing statement: " . $this->conn->error);
                return [];
            }
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
        } else {
            $result = $this->conn->query($sql);
        }

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $branches[] = $row;
            }
        } else {
            error_log("Error fetching branches: " . $this->conn->error);
        }
        return $branches;
    }

    // Get a single branch by ID
    public function getBranchById($branchId) {
        $branch = null;
        $sql = "SELECT branch_id, branch_name, country, state FROM branches WHERE branch_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $branchId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $branch = $result->fetch_assoc();
        }
        $stmt->close();
        return $branch;
    }

    // Update a branch
    public function updateBranch($branchId, $branchName, $country, $state) {
        $sql = "UPDATE branches SET branch_name = ?, country = ?, state = ? WHERE branch_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", $branchName, $country, $state, $branchId);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Error updating branch: " . $stmt->error);
            return false;
        }
        $stmt->close();
    }

    // Delete a branch
    public function deleteBranch($branchId) {
        // Optional: Add checks here to ensure no users are associated with this branch before deleting
        $sql = "DELETE FROM branches WHERE branch_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $branchId);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Error deleting branch: " . $stmt->error);
            return false;
        }
        $stmt->close();
    }
}

// The connection will be closed by the including script (e.g., admin_add_branch.php)
?>
