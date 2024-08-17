<?php
session_start();
include 'db.php'; // Include your database connection file

$userId = $_SESSION['user_id'] ?? null;

if ($userId === null) {
    header('Location: index.php');
    exit();
}

// Retrieve form data
$fullName = $_POST['full_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$city = $_POST['city'] ?? '';
$state = $_POST['state'] ?? '';
$zipCode = $_POST['zip_code'] ?? '';
$country = $_POST['country'] ?? '';
$paymentMethod = $_POST['payment_method'] ?? '';

// Fetch cart items
$stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if cart items are empty
if (empty($cartItems)) {
    echo "No items in the cart. Please add items to your cart before placing an order.";
    exit();
}

// Initialize total price
$totalPrice = 0;
$imagePaths = [];
foreach ($cartItems as $item) {
    $totalPrice += $item['item_price'] * $item['quantity'];
    $imagePaths[] = $item['item_image']; // Collect image paths for each item
}

// Add COD charge if applicable
$codCharge = 79;
if ($paymentMethod === 'cod') {
    $totalPrice += $codCharge;
}

// Function to generate a unique 6-digit order ID
function generateUniqueOrderId($pdo) {
    do {
        // Generate a random 6-digit number
        $orderId = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Check if the generated order ID already exists in the database
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $exists = $stmt->fetchColumn();
    } while ($exists > 0);

    return $orderId;
}

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Generate a unique order ID
    $orderId = generateUniqueOrderId($pdo);

    // Insert order into orders table
    $stmt = $pdo->prepare("INSERT INTO orders (id, user_id, full_name, email, phone, address, city, state, zip_code, country, total_price, shipping_method, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->execute([$orderId, $userId, $fullName, $email, $phone, $address, $city, $state, $zipCode, $country, $totalPrice, $paymentMethod]);

    // Insert each cart item into order_items table
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_name, item_price, quantity, item_image) VALUES (?, ?, ?, ?, ?)");
    foreach ($cartItems as $item) {
        $stmt->execute([$orderId, $item['item_name'], $item['item_price'], $item['quantity'], $item['item_image']]);
    }

    // Insert notification
    $notificationMessage = "New order placed by {$fullName} with Order ID: {$orderId}";
    $stmt = $pdo->prepare("INSERT INTO notifications (message) VALUES (?)");
    $stmt->execute([$notificationMessage]);

    // Commit transaction
    $pdo->commit();

    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Redirect to avoid form resubmission
    header('Location: order_summary.php?order_id=' . $orderId);
    exit();

} catch (Exception $e) {
    // Rollback transaction in case of error
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="vivo.css">
    
</head>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 15px;
            text-align: center;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        .order-summary, .shipping-details {
            margin-bottom: 30px;
        }
        .order-summary h2, .shipping-details h2 {
            color: #333;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-price {
            font-size: 18px;
            font-weight: bold;
            color: #00bcd4;
        }
        .item-image {
            max-width: 100px;
            height: auto;
        }
    </style>
<<body>
    <header>
        <a href="index.php">Home</a> | <a href="cart.php">Cart</a> | <a href="logout.php">Logout</a>
    </header>
    <div class="container">
        <h1>Order Confirmation</h1>
        
        <div class="shipping-details">
            <h2>Shipping Information</h2>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($fullName); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
            <p><strong>City:</strong> <?php echo htmlspecialchars($city); ?></p>
            <p><strong>State:</strong> <?php echo htmlspecialchars($state); ?></p>
            <p><strong>Zip Code:</strong> <?php echo htmlspecialchars($zipCode); ?></p>
            <p><strong>Country:</strong> <?php echo htmlspecialchars($country); ?></p>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($paymentMethod === 'cod' ? 'Cash on Delivery (COD)' : 'Online Payment'); ?></p>
        </div>
        
        <div class="order-summary">
            <h2>Your Order Summary</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Image</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($item['item_image']); ?>" alt="Product Image" class="item-image">
                            </td>
                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                            <td>₹<?php echo number_format($item['item_price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>₹<?php echo number_format($item['item_price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p class="total-price">Total Price: $<?php echo number_format($totalPrice, 2); ?></p>
        </div>
    </div>
</body>
</html>
