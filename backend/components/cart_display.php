<?php
// This file is intended to be included where the cart needs to be displayed.
// It assumes $conn, $_SESSION['online_cart'], $taxRateModel, $customer_balance, $delivery_fee, $message are available in the scope.
// If not, they need to be passed or initialized.

// Ensure $taxRateModel is available, if not, initialize it (e.g., for AJAX calls)
if (!isset($taxRateModel) || !($taxRateModel instanceof TaxRate)) {
    // Assuming db_connection.php is already included or can be included here
    // require_once __DIR__ . '/../database/db_connection.php'; // Uncomment if db_connection is not guaranteed
    // include_once __DIR__ . '/../models/tax_rates.php'; // Uncomment if TaxRate model is not guaranteed
    // $taxRateModel = new TaxRate($conn); // Uncomment if TaxRate model is not guaranteed
}

// Initialize variables if they might not be set (e.g., for initial load or if cart is empty)
$cartTotal = 0;
$taxRate = 0;
$taxAmount = 0;
$totalWithTax = 0;
$delivery_fee = $delivery_fee ?? 0; // Assume 0 if not set
$customer_balance = $customer_balance ?? 0; // Assume 0 if not set
$message = $message ?? ''; // Assume empty if not set

if (isset($_SESSION['online_cart']) && !empty($_SESSION['online_cart'])) {
    foreach ($_SESSION['online_cart'] as $item) {
        $itemTotal = $item['price'] * $item['qty'];
        $cartTotal += $itemTotal;
    }

    // Tax calculation for online store (customer's cart)
    $countryForTax = null;
    $stateForTax = null;

    // Get customer's country from session
    if (isset($_SESSION['country'])) {
        $countryForTax = $_SESSION['country'];
        $stateForTax = $_SESSION['state'] ?? null;
    }

    if ($countryForTax && isset($taxRateModel)) {
        $taxRate = $taxRateModel->getTaxRateByLocation($countryForTax, $stateForTax);
    }

    $taxAmount = ($cartTotal * $taxRate) / 100;
    $totalWithTax = $cartTotal + $taxAmount;
}
?>

<div class="card-body">
    <h3 class="card-title mb-3">Cart</h3>

    <?php if (isset($_SESSION['online_cart']) && !empty($_SESSION['online_cart'])): ?>
        <div class="table-responsive">
            <table class="table table-bordered cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['online_cart'] as $item):
                        $itemTotal = $item['price'] * $item['qty'];
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($item['qty']) ?></td>
                            <td><?= number_format($itemTotal, 2) ?></td>
                            <td>
                                <form method="POST" class="d-inline remove-from-cart-form">
                                    <input type="hidden" name="action" value="remove_from_cart">
                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($item['product_id']) ?>">
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <tr>
                        <td colspan="2" class="text-end fw-bold">Subtotal</td>
                        <td colspan="2" class="fw-bold">₦<?= number_format($cartTotal, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-end fw-bold">V.A.T. (<?= number_format($taxRate, 2) ?>%)</td>
                        <td colspan="2" class="fw-bold">₦<?= number_format($taxAmount, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-end fw-bold">Total</td>
                        <td colspan="2" class="fw-bold">₦<?= number_format($totalWithTax, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Buttons -->
        <div class="mt-3">
            <form method="POST" class="d-inline clear-cart-form">
                <input type="hidden" name="action" value="clear_cart">
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-x-circle"></i> Clear Cart
                </button>
            </form>

            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                <i class="bi bi-credit-card"></i> Checkout
            </button>
        </div>
    <?php else: ?>
        <p class="text-center mt-3">Your cart is empty.</p>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="alert alert-info mt-3">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Hidden fields to pass cart totals to JavaScript for modal update -->
    <input type="hidden" id="hiddenCartTotal" value="<?= htmlspecialchars(number_format($cartTotal, 2, '.', '')) ?>">
    <input type="hidden" id="hiddenTaxRate" value="<?= htmlspecialchars(number_format($taxRate, 2, '.', '')) ?>">
    <input type="hidden" id="hiddenTaxAmount" value="<?= htmlspecialchars(number_format($taxAmount, 2, '.', '')) ?>">
    <input type="hidden" id="hiddenTotalWithTax" value="<?= htmlspecialchars(number_format($totalWithTax, 2, '.', '')) ?>">
    <input type="hidden" id="hiddenDeliveryFee" value="<?= htmlspecialchars(number_format($delivery_fee, 2, '.', '')) ?>">
    <input type="hidden" id="hiddenCustomerBalance" value="<?= htmlspecialchars(number_format($customer_balance, 2, '.', '')) ?>">
</div>

<!-- Checkout Modal (needs to be outside the cart_display.php if it's a full page modal) -->
<!-- For now, assuming it's handled in online_store.php, but if it needs to be dynamic,
     its content might also need to be updated via AJAX. -->
