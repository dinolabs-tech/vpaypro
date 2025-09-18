<?php
include('database/db_connection.php');

// Get the transaction ID from the query string
$transaction_id = $_GET['transaction_id'];

// Fetch transaction details from the database
$query = "SELECT * FROM transactiondetails WHERE transactionID = '$transaction_id'";
$result = mysqli_query($conn, $query);

// Calculate cart total
$cartTotal = 0;

// Fetch cashier name
$cashier = '';

?>

<!DOCTYPE html>
<html>
<head>
  <title>Print Receipt</title>
  <style>
    body {
      font-family: 'Courier New', monospace;
      padding: 10px;
      font-size: 14px;
    }
    .receipt {
      width: 250px;
      margin: auto;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 4px;
      text-align: left;
    }
    hr {
      border: none;
      border-top: 1px dashed #000;
      margin: 8px 0;
    }
    .text-center { text-align: center; }
    .text-end { text-align: right; }
    .fw-bold { font-weight: bold; }
  </style>
  <script>
    window.onload = function() {
      window.print();
    }
  </script>
</head>
<body>
  <div class="d-none d-print-block px-3" style="font-family: 'Courier New', monospace; font-size: 14px;">
    <div class="text-center">
      <h5>🧾 VPayPro</h5>
      <p>Receipt</p>
      <p>Invoice ID: <?= htmlspecialchars($transaction_id) ?></p>
    </div>
    <hr>
    <table style="width: 100%;">
      <thead>
        <tr>
          <th>Item</th>
          <th>Qty</th>
          <th>Price</th>
          <th>Discount</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $total_amount = 0;
        while ($transaction = mysqli_fetch_assoc($result)) {
          $product_name = htmlspecialchars($transaction['productname']);
          $amount = htmlspecialchars($transaction['amount']);
          $units = htmlspecialchars($transaction['units']);
          $discount = htmlspecialchars($transaction['discount']);

          $itemTotal = $amount;
          $total_amount += $itemTotal;
          ?>
          <tr>
            <td><?= $product_name ?></td>
            <td><?= $units ?></td>
            <td><?= number_format($amount, 2) ?></td>
            <td><?= $discount ?>%</td>
            <td><?= number_format($itemTotal, 2) ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
    <hr>
    <p class="text-end fw-bold">Total: ₦<?= number_format($total_amount, 2) ?></p>
    <p class="text-center">-- Receipt Generated --</p>
  </div>
</body>
</html>
