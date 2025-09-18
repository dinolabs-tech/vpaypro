<?php
// ajax/add_to_cart.php
require_once __DIR__ . '/../database/db_connection.php';
require_once __DIR__ . '/../models/tax_rates.php';
require_once __DIR__ . '/../models/online_store.php';

session_start();

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request.'];
http_response_code(400); // Default to Bad Request

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    try {
        if (!isset($_SESSION['customer_id']) || !isset($_SESSION['user_id'])) {
            throw new Exception('Customer not logged in');
        }

        // Retrieve and sanitize product data
        $productId = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
        $productName = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $quantityToAdd = filter_input(INPUT_POST, 'qty', FILTER_SANITIZE_NUMBER_INT);
        $branchId = filter_input(INPUT_POST, 'branch_id', FILTER_SANITIZE_NUMBER_INT);

        // Basic validation
        if (!$productId || !$productName || $price === false || $quantityToAdd === null || !$branchId) {
            throw new Exception('Invalid product data received.');
        }

        // Ensure quantity to add is at least 1
        if ($quantityToAdd < 1) {
            $quantityToAdd = 1;
        }

        // Fetch available stock quantity from the database
        $stockQuery = $conn->prepare("SELECT quantity FROM branch_product_inventory WHERE productid = ? AND branch_id = ?");
        $stockQuery->bind_param("ii", $productId, $branchId);
        $stockQuery->execute();
        $stockResult = $stockQuery->get_result();
        $stockData = $stockResult->fetch_assoc();
        $availableStock = $stockData ? (int)$stockData['quantity'] : 0;
        $stockQuery->close();

        // Calculate the potential new quantity if added to cart
        $currentCartQuantity = 0;
        $productFoundInCart = false;
        if (isset($_SESSION['online_cart'])) {
            foreach ($_SESSION['online_cart'] as $item) {
                if ($item['product_id'] == $productId && $item['branch_id'] == $branchId) {
                    $currentCartQuantity = $item['qty'];
                    $productFoundInCart = true;
                    break;
                }
            }
        }
        $potentialNewQuantity = $currentCartQuantity + $quantityToAdd;

        // Check for overselling
        if ($potentialNewQuantity > $availableStock) {
            throw new Exception('Insufficient stock. Only ' . $availableStock . ' available.');
        }

        // Initialize cart if it doesn't exist
        if (!isset($_SESSION['online_cart'])) {
            $_SESSION['online_cart'] = [];
        }

        // Update cart or add new item
        if ($productFoundInCart) {
            foreach ($_SESSION['online_cart'] as &$item) {
                if ($item['product_id'] == $productId && $item['branch_id'] == $branchId) {
                    $item['qty'] = $potentialNewQuantity;
                    break;
                }
            }
            unset($item); // Unset the reference
        } else {
            $_SESSION['online_cart'][] = [
                'product_id' => $productId,
                'product_name' => $productName,
                'price' => $price,
                'qty' => $quantityToAdd,
                'branch_id' => $branchId
            ];
        }

        // Calculate financial data
        $taxRateModel = new TaxRate($conn);
        $onlineStore = new OnlineStore($conn);
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
        $message = 'Product added to cart successfully.';

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
        http_response_code(200);
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);
exit;
?>