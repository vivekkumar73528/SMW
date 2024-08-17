<?php
session_start();
include 'db.php'; // Include your database connection

// Function to verify the PhonePe payment
function verifyPhonePePayment($transactionId, $amount) {
    // Add your PhonePe API key and endpoint
    $apiKey = 'YOUR_PHONEPE_API_KEY';
    $endpoint = 'https://api.phonepe.com/v1/verifyPayment';

    // Create a verification request payload
    $payload = [
        'transactionId' => $transactionId,
        'amount' => $amount,
        'apiKey' => $apiKey
    ];

    // Initialize cURL
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    // Execute cURL request
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode the response
    $response = json_decode($response, true);

    // Check if payment is successful
    if ($response['status'] === 'success') {
        return true;
    } else {
        return false;
    }
}

// Get the transaction ID and amount from the callback
$transactionId = $_POST['transactionId'] ?? '';
$amount = $_POST['amount'] ?? '';

// Check if transaction ID and amount are present
if ($transactionId && $amount) {
    // Verify the payment
    $isPaymentValid = verifyPhonePePayment($transactionId, $amount);

    if ($isPaymentValid) {
        // Mark order as paid in the database
        $userId = $_SESSION['user_id'];
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, transaction_id, amount, status, created_at) VALUES (?, ?, ?, 'paid', NOW())");
        $stmt->execute([$userId, $transactionId, $amount]);

        // Clear the user's cart
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$userId]);

        // Redirect to order confirmation page
        header('Location: order_confirmation.php?status=success');
        exit();
    } else {
        // Payment verification failed
        header('Location: order_confirmation.php?status=failed');
        exit();
    }
} else {
    // Invalid callback data
    header('Location: order_confirmation.php?status=error');
    exit();
}
?>
