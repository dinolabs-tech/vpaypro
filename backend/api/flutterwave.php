<?php


include '../database/db_connection.php';
session_start();
// --- Handle Callback Request ---
// This section is now handled by api/customer_topup.php, so this part is removed.
// The logic here was for the AJAX callback, which is no longer needed.

// --- Initiate Flutterwave Checkout ---

// Get payment gateway details
$gatewayName = "flutterwave";
$sql = "SELECT api_key FROM payment_gateways WHERE gateway_name = ? AND is_active = 1";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("MySQL Prepare Error (get gateway): " . $conn->error);
    http_response_code(500);
    echo "Error retrieving payment gateway configuration.";
    exit;
}

$stmt->bind_param("s", $gatewayName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    error_log("Payment Gateway Not Found: " . $gatewayName);
    http_response_code(404);
    echo "Payment gateway not configured.";
    $stmt->close();
    exit;
}

$gateway = $result->fetch_assoc();
$flutterwavePublicKey = $gateway['api_key'] ?? null;

if (empty($flutterwavePublicKey)) {
    error_log("API Key Not Found for Gateway: " . $gatewayName);
    http_response_code(500);
    echo "API key not found for Flutterwave.";
    $stmt->close();
    exit;
}

$stmt->close();

// Get amount from POST request (sent from customer_dashboard.php)
$amount = $_POST['amount'] ?? null;

// Validate amount
if (!$amount || !is_numeric($amount) || $amount <= 0) {
    http_response_code(400);
    echo "Invalid amount provided.";
    exit;
}

// Get customer details from session (ensure session is started and populated)
$customerId = $_SESSION['user_id'] ?? null;
$customerEmail = $_SESSION['email'] ?? 'no-email@example.com'; // Provide a default or handle missing email
$customerName = $_SESSION['name'] ?? 'Customer'; // Provide a default or handle missing name
$customerPhone = $_SESSION['phone'] ?? ''; // Optional phone number

if (!$customerId) {
    http_response_code(401); // Unauthorized
    echo "User not logged in or session expired.";
    exit;
}

$siteLogo = "https://dinolabstech.com/logo.png"; // Replace with actual logo URL if available
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flutterwave Payment</title>
    <!-- You might want to include your site's CSS or a minimal style here -->
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; }
        .container { text-align: center; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .loading-text { margin-top: 20px; font-size: 1.1em; color: #555; }
    </style>
</head>
<body>
    <div class="container">
        <div class="loading-text">Processing your payment... Please wait.</div>
    </div>

    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const amount = <?php echo json_encode($amount); ?>;
            const siteLogo = "<?php echo $siteLogo; ?>";
            const flutterwavePublicKey = "<?php echo $flutterwavePublicKey; ?>";
            const customerId = "<?php echo $customerId; ?>";
            const customerEmail = "<?php echo $customerEmail; ?>";
            const customerName = "<?php echo $customerName; ?>";
            const customerPhone = "<?php echo $customerPhone; ?>";

            // Generate a unique transaction reference
            const txRef = "VPAYPRO_" + Date.now() + "_" + Math.random().toString(36).substr(2, 9);

            FlutterwaveCheckout({
                public_key: flutterwavePublicKey,
                tx_ref: txRef,
                amount: parseFloat(amount),
                currency: "NGN",
                payment_options: "card, banktransfer, ussd",
                customer: {
                    email: customerEmail,
                    phone_number: customerPhone,
                    name: customerName
                },
                customizations: {
                    title: "VPayPro Top-Up",
                    description: "Customer Top-Up",
                    logo: siteLogo
                },
                callback: function(response) {
                    // Handle the response from Flutterwave
                    if (response.status === "successful") {
                        // Redirect to api/customer_topup.php with transaction details
                        window.location.href = `customer_topup.php?amount=${amount}&tx_ref=${txRef}&status=${response.status}&customer_id=${customerId}`;
                    } else {
                        // Transaction failed or was cancelled by the user
                        alert('Transaction failed or was cancelled. Your balance has not been updated.');
                        window.location.href = 'customer_dashboard.php'; // Redirect to dashboard
                    }
                },
                onclose: function() {
                    // This function is called when the modal is closed by the user
                    console.log("Payment modal closed by user.");
                    alert('Payment was not completed. Please try again.');
                    window.location.href = 'customer_dashboard.php'; // Redirect to dashboard
                }
            });
        });
    </script>
</body>
</html>
