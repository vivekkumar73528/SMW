<?php
session_start();
// Include database connection
include 'db.php';

// Fetch cart items and user details
$userId = $_SESSION['user_id'] ?? null;
if ($userId === null) {
    header('Location: index.php');
    exit();
}

// Fetch cart items
$stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize total price
$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['item_price'] * $item['quantity'];
}

// Process the payment here (e.g., integrate with a payment gateway)

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Payment</title>
    <link rel="stylesheet" href="vivo.css">
</head>
<body>
    <h1>Online Payment</h1>
    <p>Total Amount: $<?php echo number_format($totalPrice, 2); ?></p>
    <!-- Include payment gateway integration here -->
    <!-- Example: Form for payment gateway -->
    <form action="payment_gateway.php" method="POST">
        <!-- Payment details here -->
        <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($totalPrice); ?>">
        <input type="submit" value="Pay Now">
    </form>
</body>
</html>
