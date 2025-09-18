<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/db_connection.php'; // adjust path

$rfilter = $_GET['filter'] ?? 'today';
$today       = date("Y-m-d");
$currentMonth= date("m");
$currentYear = date("Y");

switch ($rfilter) {
    case 'month':
        $sql = "SELECT SUM(profit) AS total
                FROM transactiondetails
                WHERE MONTH(transactiondate)= '$currentMonth'
                  AND YEAR(transactiondate)= '$currentYear'
                  AND description='Sales'";
        $label = "This Month";
        break;
    case 'year':
        $sql = "SELECT SUM(profit) AS total
                FROM transactiondetails
                WHERE YEAR(transactiondate)= '$currentYear'
                  AND description='Sales'";
        $label = "This Year";
        break;
    case 'today':
    default:
        $sql = "SELECT SUM(profit) AS total
                FROM transactiondetails
                WHERE DATE(transactiondate)= '$today'
                  AND description='Sales'";
        $label = "Today";
        break;
}

$result = $conn->query($sql);
$total = ($result && $row = $result->fetch_assoc())
       ? (float)$row['total']
       : 0;

echo json_encode([
  'total' => $total,
  'label' => $label
]);
