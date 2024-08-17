<?php
session_start();
include 'db.php';

// Fetch item details from POST request
$itemName = isset($_POST['itemName']) ? $_POST['itemName'] : null;
$itemPrice = isset($_POST['itemPrice']) ? $_POST['itemPrice'] : null;
$itemImage = isset($_POST['itemImage']) ? $_POST['itemImage'] : null;

if (!$itemName || !$itemPrice || !$itemImage) {
    die("Item name, price, and image are required.");
}

// Assume userId is set in session, retrieve it
$userId = $_SESSION['user_id'] ?? null;

if ($userId === null) {
    die("User not logged in.");
}

try {
    // Check if the item already exists in the cart for the user
    $stmt_check = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND item_name = ?");
    $stmt_check->execute([$userId, $itemName]);
    $existingItem = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($existingItem) {
        // Item exists, update the quantity
        $stmt_update = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND item_name = ?");
        $stmt_update->execute([$userId, $itemName]);
    } else {
        // Item doesn't exist, insert new item
        $stmt_insert = $pdo->prepare("INSERT INTO cart (user_id, item_name, item_price, item_image, quantity) VALUES (?, ?, ?, ?, 1)");
        $stmt_insert->execute([$userId, $itemName, $itemPrice, $itemImage]);
    }

    echo "Item added to cart successfully!";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
