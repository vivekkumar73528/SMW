<?php
session_start();
include 'db.php';

$totalPrice = $_GET['total_price'] ?? 0;

if (!isset($totalPrice) || $totalPrice <= 0) {
    die('Invalid total price');
}

// PayPal payment processing
$paypalUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr'; // PayPal Sandbox URL
$paypalEmail = 'your-paypal-email@example.com'; // PayPal Email for receiving payments

// Prepare PayPal payment request
$params = [
    'cmd' => '_xclick',
    'business' => $paypalEmail,
    'item_name' => 'Order Payment',
    'amount' => $totalPrice,
    'currency_code' => 'USD',
    'return' => 'http://yourdomain.com/payment_success.php', // URL to redirect after payment success
    'cancel_return' => 'http://yourdomain.com/payment_cancel.php', // URL to redirect after payment cancellation
    'notify_url' => 'http://yourdomain.com/payment_notify.php', // URL for IPN notifications
];

// Generate PayPal payment form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment</title>
</head>
<body>
    <h1>Processing Payment</h1>
    <form id="paypal-form" action="<?php echo htmlspecialchars($paypalUrl); ?>" method="POST">
        <?php foreach ($params as $key => $value): ?>
            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
        <?php endforeach; ?>
        <input type="submit" value="Pay with PayPal">
    </form>
    <script>
        document.getElementById('paypal-form').submit();
    </script>
</body>
</html>
