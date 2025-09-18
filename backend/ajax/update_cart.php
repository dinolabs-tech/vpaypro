<?php
// ajax/update_cart.php
require_once __DIR__ . '/../database/db_connection.php';
require_once __DIR__ . '/../models/tax_rates.php';
require_once __DIR__ . '/../models/online_store.php';

session_start();

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request.'];
http_response_code(400); // Default to Bad Request

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if (!isset($_SESSION['customer_id']) || !isset($_SESSION['user_id'])) {
            throw new Exception('Customer not logged in');
        }

        $action = $_POST['action'];
        $taxRateModel = new TaxRate($conn);
        $onlineStore = new OnlineStore($conn);

        switch ($action) {
            case 'remove_from_cart':
                $productId = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
                if ($productId && isset($_SESSION['online_cart'])) {
                    foreach ($_SESSION['online_cart'] as $key => $item) {
                        if ($item['product_id'] == $productId) {
                            unset($_SESSION['online_cart'][$key]);
                            break;
                        }
                    }
                    $_SESSION['online_cart'] = array_values($_SESSION['online_cart']);
                    $message = 'Product removed from cart.';
                    http_response_code(200);
                } else {
                    throw new Exception('Invalid product ID or cart is empty.');
                }
                break;

            case 'clear_cart':
                if (isset($_SESSION['online_cart'])) {
                    $_SESSION['online_cart'] = [];
                    $message = 'Cart cleared.';
                    http_response_code(200);
                } else {
                    throw new Exception('Cart is already empty.');
                }
                break;

            default:
                throw new Exception('Unknown action.');
        }

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

        // Generate cart HTML
        ob_start();
        include __DIR__ . '/../components/cart_display.php';
        $cartHtml = ob_get_clean();

        $response = [
            'status' => 'success',
            'message' => $message,
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
}

echo json_encode($response);
exit;
?>