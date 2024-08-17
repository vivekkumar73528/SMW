<?php
session_start();
include 'db.php';

$userId = $_SESSION['user_id'] ?? null;
if ($userId === null) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];
    $productName = $_POST['product_name'];
    $reason = $_POST['reason'];

    $stmt = $pdo->prepare("INSERT INTO returns (user_id, order_id, product_name, reason) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $orderId, $productName, $reason]);

    header('Location: return_success.php');
    exit();
}
?>
