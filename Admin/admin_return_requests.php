<?php
session_start();
include '../db.php'; // Ensure this file has the correct PDO connection setup

// Check if the user is an admin
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

// Retrieve and sanitize input
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'requested_at';
$page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
$itemsPerPage = 10;
$offset = ($page - 1) * $itemsPerPage;
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

// Build the search and filter query
$searchQuery = '';
$params = [];

if ($search) {
    $searchQuery .= " WHERE (r.product_name LIKE :search OR u.username LIKE :search OR r.order_id LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($startDate && $endDate) {
    $searchQuery .= $search ? " AND" : " WHERE";
    $searchQuery .= " r.requested_at BETWEEN :start_date AND :end_date";
    $params[':start_date'] = $startDate . ' 00:00:00';
    $params[':end_date'] = $endDate . ' 23:59:59';
}

// Prepare sorting query
$orderBy = match ($sort) {
    'username' => 'u.username ASC',
    'order_id' => 'r.order_id ASC',
    default => 'r.requested_at DESC',
};

// Prepare and execute the main query
$query = "
    SELECT r.id AS return_id, r.user_id, r.order_id, r.product_name, r.reason, r.requested_at,
           u.username, u.email, o.total_price, o.shipping_method, o.status, o.address, o.city, o.state, o.zip_code, o.country, oi.item_image
    FROM returns r
    JOIN users u ON r.user_id = u.id
    JOIN orders o ON r.order_id = o.id
    JOIN order_items oi ON r.order_id = oi.order_id AND oi.product_name = r.product_name
    $searchQuery
    ORDER BY $orderBy
    LIMIT $itemsPerPage OFFSET $offset
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$returnRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total results
$countQuery = "
    SELECT COUNT(*) AS total
    FROM returns r
    JOIN users u ON r.user_id = u.id
    JOIN orders o ON r.order_id = o.id
    JOIN order_items oi ON r.order_id = oi.order_id AND oi.product_name = r.product_name
    $searchQuery
";

$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($params);
$totalResults = $countStmt->fetchColumn();
$totalPages = ceil($totalResults / $itemsPerPage);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Requests - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
      body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background-color: #f5f7fa;
        color: #4d4d4d;
      }
      .sidebar {
        width: 250px;
        background-color: #2c3e50;
        color: white;
        position: fixed;
        height: 100%;
        padding-top: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: transform 0.3s ease;
        transform: translateX(0);
        z-index: 1000;
      }
      .sidebar a {
        color: white;
        text-decoration: none;
        margin: 15px 0;
        display: block;
        width: 100%;
        text-align: center;
        padding: 10px 0;
        transition: background-color 0.3s;
        font-size: 1rem;
      }
      .sidebar a:hover {
        background-color: #34495e;
      }
      .sidebar .close-btn {
        display: none;
        font-size: 30px;
        cursor: pointer;
        padding: 10px;
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #2c3e50;
        color: white;
        border-radius: 50%;
        z-index: 1001;
      }
      .main-content {
        margin-left: 250px;
        padding: 20px;
        transition: margin-left 0.3s ease;
      }
      .toggle-sidebar {
        display: none;
        font-size: 30px;
        cursor: pointer;
        padding: 10px;
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 10;
        background-color: #2c3e50;
        color: white;
        border-radius: 5px;
      }
      .navbar {
        display: none;
        background-color: #2c3e50;
      }
      /* .btn .btn-outline-secondary  {
        text-decoration: none;
      } */
      @media (max-width: 768px) {
        .sidebar {
          transform: translateX(-250px);
        }
        .sidebar.visible {
          transform: translateX(0);
        }
        .sidebar .close-btn {
          display: block;
        }
        .main-content {
          margin-left: 0;
        }
        .toggle-sidebar {
          display: block;
        }
      }
      .container {
        padding: 2rem;
      }
      .search-filter-area {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px 20px;
        margin-bottom: 20px;
       
      }
      .search-filter-area h2 {
        margin-bottom: 20px;
        font-size: 1.5rem;
        color: #333;
      }
      .search-filter-area .form-control {
        border-radius: 5px;
        border: 1px solid #ced4da;
      }
      .search-filter-area .form-select {
        border-radius: 5px;
        border: 1px solid #ced4da;
      }
      .search-filter-area .btn-custom {
        border-radius: 5px;
        font-size: 16px;
      }
      .search-filter-area input{
        padding: 12px 14px;
        margin: 1rem;
      }
      .search-filter-area select{
        padding: 10px 12px;
        margin-bottom: 12px;
        margin-top: 12px;
      }
      .return-request-card {
        margin-bottom: 20px;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background-color: #ffffff;
        display: flex;
        gap: 20px;
        align-items: center;
      }
      .return-request-card img {
        max-width: 150px;
        border-radius: 8px;
      }
      .return-request-card .card-info {
        flex: 1;
      }
      .return-request-card h4 {
        margin-bottom: 10px;
        font-size: 1.25rem;
        color: #333;
      }
      .return-request-card p {
        margin: 5px 0;
        line-height: 1.5;
      }
      .btn-custom {
        background-color: #007bff;
        color: #ffffff;
        border-radius: 5px;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
      }
      .btn-custom:hover {
        background-color: #0056b3;
        color: #ffffff;
        text-decoration: none;
      }
      .alert {
        margin-top: 20px;
      }
      .pagination {
        margin-top: 20px;
      }
      .pagination .page-link {
        border-radius: 5px;
        padding: 8px 12px;
      }
      .pagination .page-item.active .page-link {
        background-color: #007bff;
        color: #ffffff;
        border: none;
      }
      .pagination .page-item.disabled .page-link {
        background-color: #e9ecef;
        color: #6c757d;
        border: none;
      }
      .search input{
     
        width: 90%;
        margin-left: 0rem;
        /* margin-left: 10rem; */
      }

    </style>
</head>
<body>
    <div class="sidebar">
        <div class="close-btn" onclick="toggleSidebar()"><i class="fas fa-times"></i></div>
        <h2>Admin Panel</h2>
        <a href="./admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="return_requests.php" class="active"><i class="fas fa-undo"></i> Return Requests</a>
        <a href="orders.php"><i class="fas fa-box"></i> Orders</a>
        <a href="users.php"><i class="fas fa-users"></i> Users</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="main-content">
        <div class="toggle-sidebar" onclick="toggleSidebar()"><i class="fas fa-bars"></i></div>
        <div class="container">
            <div class="search-filter-area">
                <h2>Search and Filter</h2>
                <!-- Search Form -->
                <form method="GET" action="" class="mb-4">
                    <div class="search">
                        <input type="text" name="search" class="form-control" placeholder="Search by Product Name, Username, or Order ID" value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                    <input type="hidden" name="page" value="<?php echo htmlspecialchars($page); ?>">
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-custom">Search</button>
                        <a href="./admin_return_requests.php" class="btn btn-outline-secondary" style="margin-left: 1rem; text-tecoration: none;" >Clear</a>
                    </div>
                </form>

                <!-- Filter Form -->
                <form method="GET" action="" class="mb-4">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                    <input type="hidden" name="page" value="<?php echo htmlspecialchars($page); ?>">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" id="start_date" value="<?php echo htmlspecialchars($startDate); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" id="end_date" value="<?php echo htmlspecialchars($endDate); ?>">
                    </div>
                    <button type="submit" class="btn btn-custom">Filter</button>
                </form>

                <!-- Sorting Form -->
                <form method="GET" action="" class="mb-4">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="hidden" name="page" value="<?php echo htmlspecialchars($page); ?>">
                    <div class="mb-3">
                        <select name="sort" class="form-select">
                            <option value="requested_at" <?php echo ($sort === 'requested_at' ? 'selected' : ''); ?>>Sort by Request Date</option>
                            <option value="username" <?php echo ($sort === 'username' ? 'selected' : ''); ?>>Sort by Username</option>
                            <option value="order_id" <?php echo ($sort === 'order_id' ? 'selected' : ''); ?>>Sort by Order ID</option>
                        </select>
                    </div>
                    <button style="margin-bottom: 1rem;" type="submit" class="btn btn-custom">Sort</button>
                </form>

                <!-- Export Button -->
                <form method="POST" action="export_csv.php" class="mb-4">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                    <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>">
                    <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>">
                    <button type="submit" class="btn btn-custom">Export to CSV</button>
                </form>
            </div>

            <?php if ($returnRequests): ?>
                <!-- Display Return Requests -->
                <?php foreach ($returnRequests as $request): ?>
                    <div class="return-request-card">
                        <img src="<?php echo htmlspecialchars($request['item_image']); ?>" alt="Product Image">
                        <div class="card-info">
                            <h4>Return ID: <?php echo htmlspecialchars($request['return_id']); ?></h4>
                            <p><strong>Username:</strong> <?php echo htmlspecialchars($request['username']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($request['email']); ?></p>
                            <p><strong>Order ID:</strong> <?php echo htmlspecialchars($request['order_id']); ?></p>
                            <p><strong>Product Name:</strong> <?php echo htmlspecialchars($request['product_name']); ?></p>
                            <p><strong>Reason for Return:</strong> <?php echo htmlspecialchars($request['reason']); ?></p>
                            <p><strong>Requested At:</strong> <?php echo htmlspecialchars($request['requested_at']); ?></p>
                            <p><strong>Total Price:</strong> $<?php echo number_format($request['total_price'], 2); ?></p>
                            <p><strong>Shipping Method:</strong> <?php echo htmlspecialchars($request['shipping_method']); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($request['status']); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($request['address']); ?></p>
                            <p><strong>City:</strong> <?php echo htmlspecialchars($request['city']); ?></p>
                            <p><strong>State:</strong> <?php echo htmlspecialchars($request['state']); ?></p>
                            <p><strong>ZIP Code:</strong> <?php echo htmlspecialchars($request['zip_code']); ?></p>
                            <p><strong>Country:</strong> <?php echo htmlspecialchars($request['country']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No return requests found.</p>
            <?php endif; ?>

            <!-- Pagination Controls -->
           
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
      function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('visible');
      }
    </script>
</body>
</html>
