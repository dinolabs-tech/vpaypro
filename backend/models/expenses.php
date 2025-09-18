<?php


include './database/db_connection.php';
session_start();


// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id = isset($_POST["id"]) ? $_POST["id"] : 0;
  $description = $_POST["description"];
  $amount = $_POST["amount"];
  $date = $_POST["date"];

  if ($id > 0) {
    // Update expense
    $stmt = $conn->prepare("UPDATE expenses SET description = ?, amount = ?, date = ? WHERE id = ?");
    $stmt->bind_param("sdsi", $description, $amount, $date, $id);
    if ($stmt->execute()) {
      echo "<script>alert('Expense updated successfully!'); window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
    } else {
      echo "Error updating expense: " . $stmt->error;
    }
    $stmt->close();
  } else {
    // Insert new expense
    $stmt = $conn->prepare("INSERT INTO expenses (description, amount, date) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $description, $amount, $date);
    if ($stmt->execute()) {
      $expense_id = $conn->insert_id; // Get the last inserted ID

      // Insert into transactiondetails table
      $transactionID = uniqid(); // Generate a unique transaction ID
      $productid = 0; // Assuming 0 for expenses
      $productname = "Expense"; // Assuming "Expense" for product name
      $units = 1; // Assuming 1 unit for expenses
      $profit = 0; // Assuming 0 profit for expenses
      $cashier = $_SESSION['staffname'] ?? 'Unknown';
      $status = "expense"; // Assuming "expense" for status
      $discount = 0; // Assuming 0 discount for expenses
      $transactiondate = date('Y-m-d H:i:s', strtotime($date));

      $stmt_trans = $conn->prepare("INSERT INTO transactiondetails (transactionID, productid, productname, description, units, amount, transactiondate, profit, cashier, status, discount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt_trans->bind_param("sisssdsdsss", $transactionID, $productid, $productname, $description, $units, $amount, $transactiondate, $profit, $cashier, $status, $discount);

      if ($stmt_trans->execute()) {
        echo "<script>alert('Expense saved successfully!'); window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
      } else {
        echo "Error inserting into transactiondetails: " . $stmt_trans->error;
      }
      $stmt_trans->close();
    } else {
      echo "Error inserting into expenses: " . $stmt->error;
    }
    $stmt->close();
  }
  
}

function get_expenses($conn) {
  $sql = "SELECT * FROM expenses";
  $result = $conn->query($sql);

  $expenses = array();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $expenses[] = $row;
    }
  }
  return $expenses;
}


?>
