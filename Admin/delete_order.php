<?php
session_start();
include '../db.php';

// Check if the user is an admin
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

$orderId = $_GET['id'] ?? null;

if ($orderId) {
    try {
        // Begin a transaction
        $pdo->beginTransaction();

        // Soft delete related order items
        $stmt = $pdo->prepare("UPDATE order_items SET deleted_at = NOW() WHERE order_id = ?");
        $stmt->execute([$orderId]);

        // Soft delete the order
        $stmt = $pdo->prepare("UPDATE orders SET deleted_at = NOW() WHERE id = ?");
        $stmt->execute([$orderId]);

        // Commit the transaction
        $pdo->commit();
    } catch (Exception $e) {
        // Rollback the transaction if something fails
        $pdo->rollBack();
        echo "Failed to delete order: " . $e->getMessage();
        exit();
    }
}

header('Location: admin_dashboard.php');
exit();
?>
