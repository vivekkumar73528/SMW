<?php session_start();
include 'db.php';

// Ensure user is logged in
$userId =$_SESSION['user_id'] ?? null;

if ($userId ===null) {
    header('Location: index.php');
    exit();
}

// Initialize filter variables
$statusFilter =$_GET['status'] ?? '';
$startDate =$_GET['start_date'] ?? '';
$endDate =$_GET['end_date'] ?? '';

// Build the SQL query with filters
$sql ="SELECT * FROM orders WHERE user_id = ?";
$params =[$userId];

if ($statusFilter) {
    $sql .=" AND status = ?";
    $params[]=$statusFilter;
}

if ($startDate && $endDate) {
    $sql .=" AND created_at BETWEEN ? AND ?";
    $params[]=$startDate . ' 00:00:00';
    $params[]=$endDate . ' 23:59:59';
}

$sql .=" ORDER BY created_at DESC";

// Fetch user's orders
$stmt =$pdo->prepare($sql);
$stmt->execute($params);
$orders =$stmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination
$itemsPerPage =10;
$totalOrders =count($orders);
$totalPages =ceil($totalOrders / $itemsPerPage);
$page =$_GET['page'] ?? 1;
$offset =($page - 1) * $itemsPerPage;

// Fetch orders for current page
$sql .=" LIMIT ? OFFSET ?";
$params[]=$itemsPerPage;
$params[]=$offset;

$stmt =$pdo->prepare($sql);
$stmt->execute($params);
$orders =$stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="vivo.css">
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    header {
        background-color: brown;
        color: white;
        padding: 15px;
        text-align: center;
    }

    h1,
    h2 {
        color: brown;
    }

    form {
        max-width: 800px;
        margin: 20px auto;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    label {
        display: block;
        margin-top: 10px;
        font-weight: bold;
    }

    input[type="text"],
    input[type="date"],
    select {
        width: calc(100% - 22px);
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }

    input[type="submit"] {
        background-color: brown;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 10px;
        cursor: pointer;
        margin-top: 10px;
    }

    input[type="submit"]:hover {
        background-color: darkred;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    table th,
    table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }

    table th {
        background-color: #f4f4f4;
    }

    table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    .pagination a {
        padding: 10px 15px;
        border: 1px solid #ddd;
        margin: 0 5px;
        text-decoration: none;
        color: brown;
    }

    .pagination a.active {
        background-color: brown;
        color: white;
    }
</style>


<body>
    <header>
        <a href="index.php">Home</a>| <a href="cart.php">Cart</a>| <a href="order_history.php">Order
            History</a>| <a href="logout.php">Logout</a>
    </header>
    <h1>Order History</h1>
    <form action="order_history.php" method="GET">
        <h2>Filter Orders</h2><label for="status">Status:</label><select id="status" name="status">
            <option value="">All</option>
            <option value="pending" <?php echo $statusFilter==='pending' ? 'selected' : '' ; ?>>Pending</option>
            <option value="completed" <?php echo $statusFilter==='completed' ? 'selected' : '' ; ?>>Completed
            </option>
            <option value="canceled" <?php echo $statusFilter==='canceled' ? 'selected' : '' ; ?>>Canceled</option>
        </select><label for="start_date">Start Date:</label><input type="date" id="start_date" name="start_date"
            value="<?php echo htmlspecialchars($startDate); ?>"><label for="end_date">End Date:</label><input
            type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>"><input
            type="submit" value="Filter">
    </form>
    <?php if ( !empty($orders)): ?>
    <?php foreach ($orders as $order): ?>
    <div class="order-details">
        <h2>Order ID:
            <?php echo htmlspecialchars($order['id']);
?>
        </h2>
        <p><strong>Date:</strong>
            <?php echo htmlspecialchars(date('F j, Y', strtotime($order['created_at'])));
?>
        </p>
        <p><strong>Status:</strong>
            <?php echo htmlspecialchars(ucfirst($order['status']));
?>
        </p>
        <p><strong>Total Price:</strong>₹
            <?php echo number_format($order['total_price'], 2);
?>
        </p>
        <?php // Fetch items for this order
$stmt_items =$pdo->prepare("SELECT * FROM orders_items WHERE order_id = ?");
$stmt_items->execute([$order['id']]);
$orderItems =$stmt_items->fetchAll(PDO::FETCH_ASSOC);
?>
        <h3>Items Ordered:</h3>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($item['item_name']);
?>
                    </td>
                    <td>₹
                        <?php echo number_format($item['item_price'], 2);
?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($item['quantity']);
?>
                    </td>
                    <td>₹
                        <?php echo number_format($item['item_price'] * $item['quantity'], 2);
?>
                    </td>
                </tr>
                <?php endforeach;
?>
            </tbody>
        </table>
    </div>
    <?php endforeach;
?>
    <!-- Pagination -->
        <div class="pagination">
            <?php for ($i =1; $i <=$totalPages; $i++): ?><a
                href="order_history.php?page=<?php echo $i; ?>&status=<?php echo urlencode($statusFilter); ?>&start_date=<?php echo urlencode($startDate); ?>&end_date=<?php echo urlencode($endDate); ?>"
                class="<?php echo $i == $page ? 'active' : ''; ?>">
                <?php echo $i;
?>
            </a>
            <?php endfor;
?>
        </div>
        <?php else: ?>
        <p>You have no orders yet.</p>
        <?php endif;
?>
</body>

</html>