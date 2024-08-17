<?php
session_start();
include '../db.php'; // Ensure this file has the correct PDO connection setup

// Check if the user is an admin
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

// Handle search, sorting, and date range filtering
$search = $_POST['search'] ?? '';
$sort = $_POST['sort'] ?? 'requested_at';
$startDate = $_POST['start_date'] ?? '';
$endDate = $_POST['end_date'] ?? '';

// Prepare search query
$searchQuery = '';
$params = [];

if ($search) {
    $searchQuery = "WHERE r.product_name LIKE ? OR u.username LIKE ? OR r.order_id LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%"];
}

if ($startDate && $endDate) {
    $searchQuery .= $searchQuery ? " AND" : " WHERE";
    $searchQuery .= " r.requested_at BETWEEN ? AND ?";
    $params[] = $startDate . ' 00:00:00';
    $params[] = $endDate . ' 23:59:59';
}

// Prepare sorting query
$orderBy = '';
switch ($sort) {
    case 'username':
        $orderBy = 'u.username ASC';
        break;
    case 'order_id':
        $orderBy = 'r.order_id ASC';
        break;
    default:
        $orderBy = 'r.requested_at DESC';
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="return_requests.csv"');

$output = fopen('php://output', 'w');

// Output header
fputcsv($output, ['Return ID', 'Username', 'Email', 'Order ID', 'Product Name', 'Reason for Return', 'Requested At', 'Total Price', 'Shipping Method', 'Status', 'Address', 'City', 'State', 'ZIP Code', 'Country']);

$stmt = $pdo->prepare("
    SELECT r.id AS return_id, r.user_id, r.order_id, r.product_name, r.reason, r.requested_at,
           u.username, u.email, o.total_price, o.shipping_method, o.status, o.address, o.city, o.state, o.zip_code, o.country
    FROM returns r
    JOIN users u ON r.user_id = u.id
    JOIN orders o ON r.order_id = o.id
    JOIN order_items oi ON r.order_id = oi.order_id AND oi.product_name = r.product_name
    $searchQuery
    ORDER BY $orderBy
");

$stmt->execute($params);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['return_id'],
        $row['username'],
        $row['email'],
        $row['order_id'],
        $row['product_name'],
        $row['reason'],
        $row['requested_at'],
        $row['total_price'],
        $row['shipping_method'],
        $row['status'],
        $row['address'],
        $row['city'],
        $row['state'],
        $row['zip_code'],
        $row['country']
    ]);
}

fclose($output);
exit();
?>
