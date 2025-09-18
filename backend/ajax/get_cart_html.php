<?php
// ajax/get_cart_html.php
require_once __DIR__ . '/../database/db_connection.php';
require_once __DIR__ . '/../models/tax_rates.php';
require_once __DIR__ . '/../models/online_store.php';

session_start();

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => ''];

try {
    if (!isset($_SESSION['customer_id']) || !isset($_SESSION['user_id'])) {
        throw new Exception('Customer not logged in');
    }

    // Initialize models
    $taxRateModel = new TaxRate($conn);
    $onlineStore = new OnlineStore($conn);

    // Calculate financial data
    $cartItems = $_SESSION['online_cart'] ?? [];
    $cartTotal = 0;
    foreach ($cartItems as $item) {
        $cartTotal += $item['price'] * $item['qty'];
    }
    $countryForTax = $_SESSION['country'] ?? null;
    $stateForTax = $_SESSION['state'] ?? null;
    $taxRate = $taxRateModel->getTaxRateByLocation($countryForTax, $stateForTax);
    $taxAmount = $cartTotal * ($taxRate / 100);
    $totalWithTax = $cartTotal + $taxAmount;
    $sql = "SELECT setting_value FROM settings WHERE setting_key = 'delivery_fee'";
    $result = $conn->query($sql);
    $delivery_fee = ($result && $result->num_rows > 0) ? (float)$result->fetch_assoc()['setting_value'] : 500;
    $customer_balance = $onlineStore->getCustomerBalance($_SESSION['user_id']);
    $message = '';

    // Generate cart HTML
    ob_start();
    include __DIR__ . '/../components/cart_display.php';
    $cartHtml = ob_get_clean();

    $response = [
        'status' => 'success',
        'html' => $cartHtml,
        'cartTotal' => number_format($cartTotal, 2, '.', ''),
        'taxRate' => number_format($taxRate, 2, '.', ''),
        'taxAmount' => number_format($taxAmount, 2, '.', ''),
        'totalWithTax' => number_format($totalWithTax, 2, '.', ''),
        'deliveryFee' => number_format($delivery_fee, 2, '.', ''),
        'customerBalance' => number_format($customer_balance, 2, '.', '')
    ];
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
?>