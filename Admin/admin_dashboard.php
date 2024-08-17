    <?php
    session_start();
    include '../db.php';

    // Check if the user is an admin
    if (!isset($_SESSION['admin_logged_in'])) {
        header('Location: admin_login.php');
        exit();
    }

    // Fetch orders based on search and filter criteria
    $search = $_GET['search'] ?? '';
    $statusFilter = $_GET['status'] ?? '';
    $dateFilter = $_GET['date'] ?? '';

    // Prepare the query with filters
    $query = "
        SELECT 
            o.id, 
            o.full_name, 
            o.email, 
            o.phone, 
            o.address, 
            o.city, 
            o.state, 
            o.zip_code, 
            o.country, 
            o.total_price, 
            o.status, 
            o.created_at 
        FROM orders o
        WHERE o.deleted_at IS NULL"; // Exclude soft-deleted orders

    $params = [];

    if ($search) {
        $query .= " AND (o.full_name LIKE ? OR o.email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($statusFilter) {
        $query .= " AND o.status = ?";
        $params[] = $statusFilter;
    }

    if ($dateFilter) {
        $query .= " AND DATE(o.created_at) = ?";
        $params[] = $dateFilter;
    }

    $query .= " ORDER BY o.created_at DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate summary statistics
    $summaryQuery = "SELECT COUNT(*) as total_orders, SUM(total_price) as total_sales FROM orders WHERE deleted_at IS NULL";
    $summaryStmt = $pdo->prepare($summaryQuery);
    $summaryStmt->execute();
    $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

    // Fetch user data
    $userQuery = "SELECT id, username, email, created_at FROM users";
    $userStmt = $pdo->prepare($userQuery);
    $userStmt->execute();
    $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch notifications for new orders
    $query = "
        SELECT 
            o.id, 
            o.full_name, 
            o.total_price, 
            o.created_at 
        FROM orders o
        WHERE o.status = 'Pending' AND o.deleted_at IS NULL
        ORDER BY o.created_at DESC
        LIMIT 10";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Delete notification
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_notification'])) {
        $notifId = $_POST['notification_id'];
        $deleteNotifQuery = "UPDATE orders SET deleted_at = NOW() WHERE id = ?";
        $deleteNotifStmt = $pdo->prepare($deleteNotifQuery);
        $deleteNotifStmt->execute([$notifId]);
        header('Location: admin_dashboard.php');
        exit();
    }

    // Fetch user form data
    $query = "SELECT * FROM userform";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $userForms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch total number of user forms
    $userFormsQuery = "SELECT COUNT(*) as total_userforms FROM userform";
    $userFormsStmt = $pdo->prepare($userFormsQuery);
    $userFormsStmt->execute();
    $userFormsSummary = $userFormsStmt->fetch(PDO::FETCH_ASSOC);

    // Extract summary data
    $totalOrders = $summary['total_orders'];
    $totalSales = $summary['total_sales'];
    $totalUserForms = $userFormsSummary['total_userforms'];
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
/* styles.css */
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background-color: #f5f7fa;
    color: #4d4d4d;
}

/* Sidebar styling */
.sidebar {
    width: 250px;
    background-color: #324a5e;
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
    background-color: #3f5a73;
}

/* Main content styling */
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
    background-color: #324a5e;
    color: white;
    border-radius: 5px;
}

/* Navbar styling */
.navbar {
    display: none;
    background-color: #324a5e;
}

@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-250px);
    }

    .sidebar.visible {
        transform: translateX(0);
    }

    .main-content {
        margin-left: 0;
    }

    .toggle-sidebar {
        display: block;
    }

    .navbar {
        display: flex;
        width: 100%;
    }
}

/* Summary section styling */
.summary {
    display: flex;
    justify-content: space-around;
    margin-bottom: 20px;
}

.summary-item {
    background-color: #ffffff;
    border-radius: 5px;
    padding: 20px;
    text-align: center;
    width: 30%;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
    border-top: 5px solid #007bff;
}

.summary-item:hover {
    transform: translateY(-5px);
}

.summary-item i {
    font-size: 2rem;
    margin-bottom: 10px;
    color: #007bff;
}

/* Table styling */
.table-wrapper {
    overflow-x: auto;
    background-color: #ffffff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.table-wrapper table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.table-wrapper th, .table-wrapper td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

.table-wrapper th {
    background-color: #f8f9fa;
    color: #324a5e;
}

.table-wrapper tr:nth-child(even) {
    background-color: #f2f2f2;
}

.no-data {
    text-align: center;
    padding: 10px;
}

/* Filter form styling */
.filter-form {
    display: flex;
    justify-content: space-around;
    margin-bottom: 20px;
    background-color: #ffffff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.filter-form div {
    display: flex;
    flex-direction: column;
}

.filter-form label {
    margin-bottom: 5px;
}

.filter-form input, .filter-form select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
}
.filter-form input[type="submit"]
{
    background-color:#007bff; 
    color: white;
}
.actions a {

    text-decoration: none;
    padding: 18px;
    color: #9b23b9;
}

.actions a:hover {
    text-decoration: underline;
}

/* Notification styling */
.notification {
    display: flex;
    align-items: center;
    background-color: #ffffff;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.notification i {
    margin-right: 10px;
    font-size: 1.5rem;
    color: #ff6b6b;
}


.notification .details {
    display: flex;
    justify-content: space-between;
    width: 100%;
}

.notification .details .info {
    display: flex;
    flex-direction: column;
}

.notification .details .info .order-info {
    font-weight: bold;
}

.notification .details .info .time {
    color: #6c757d;
}

.delete-notif {
    background: none;
    border: none;
    cursor: pointer;
    color: #dc3545;
}

/* User Contact Form Table Styling */
.user-forms-table {
    overflow-x: auto;
    background-color: #ffffff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-top: 20px;
}

.user-forms-table table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.user-forms-table th, .user-forms-table td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

.user-forms-table th {
    background-color: #e9ecef;
    color: #495057;
}

.user-forms-table tr:nth-child(even) {
    background-color: #f2f2f2;
}


@media (max-width: 768px) {
    .summary {
        flex-direction: column;
        align-items: center;
    }

    .summary-item {
        width: 80%;
        margin-bottom: 20px;
    }

    .filter-form {
        flex-direction: column;
    }

    .filter-form div {
        width: 100%;
        margin-bottom: 10px;
    }

    .filter-form div:last-child {
        margin-bottom: 0;
    }
}


    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="./admin_dashboard.php">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#" onclick="showDashboard()">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="showUsersTable()">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="showOrdersTable()">Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="showUserFormsTable()">User Forms</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="./view_feedback.php" onclick="showReports()">Feedbacks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./admin_return_requests.php" onclick="showReports()">Return</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- <aside class="sidebar" id="sidebar">
        <a href="./admin_dashboard.php" onclick="showDashboard()">Dashboard</a>
        <a href="#" onclick="showUsersTable()">Users</a>
        <a href="#" onclick="showOrdersTable()">Orders</a>
        <a href="#" onclick="showUserFormsTable()">User Forms</a>
        <a class="nav-link" href="./admin_return_requests.php" onclick="showReports()">Return</a>
        <a href="admin_logout.php">Logout</a>
    </aside> -->

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="./admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="orders.php" onclick="showOrdersTable()"><i class="fas fa-box"></i> Orders</a>
        <a href="users.php" onclick="showUsersTable()"><i class="fas fa-users"></i> Users</a>
        <a href="#" onclick="showUserFormsTable()"> <i class="fas fa-envelope"></i> User Forms </a>
        <a href="./view_feedback.php" onclick="showReports()"> <i class="fas fa-pencil-alt"></i> Feedbacks</a>
        <a href="./admin_return_requests.php" class="active"><i class="fas fa-undo"></i> Return Requests</a>
        <a href="settings.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <main class="main-content">
        <div class="container">
            <!-- Summary Section -->
            <div class="summary">
                <div class="summary-item">
                    <i class="fas fa-shopping-cart"></i>
                    <h2>Total Orders</h2>
                    <p><?php echo htmlspecialchars($totalOrders); ?></p>
                </div>
                <div class="summary-item">
                    <i class="fas fa-dollar-sign"></i>
                    <h2>Total Sales</h2>
                    <p>₹<?php echo number_format($totalSales, 2); ?></p>
                </div>
                <div class="summary-item">
                    <i class="fas fa-users"></i>
                    <h2>User Forms</h2>
                    <p><?php echo htmlspecialchars($totalUserForms); ?></p>
                </div>
            </div>

            <!-- Notifications Section -->
            <div class="notifications">
                <h2 style="color: orange;"><i class="fas fa-bell"></i> New Order Notifications</h2>
                <?php if ($notifications): ?>
                    <?php foreach ($notifications as $notification): ?>
                        <div class="notification">
                            <i class="fas fa-box"></i>
                            <div class="details">
                                <div class="info">
                                    <p class="order-info">Order #<?php echo htmlspecialchars($notification['id']); ?> by <?php echo htmlspecialchars($notification['full_name']); ?></p>
                                    <p>Total: ₹<?php echo number_format($notification['total_price'], 2); ?></p>
                                    <p class="time"><?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($notification['created_at']))); ?></p>
                                </div>
                                <form method="POST" action="">
                                    <input type="hidden" name="notification_id" value="<?php echo htmlspecialchars($notification['id']); ?>">
                                    <button type="submit" name="delete_notification" class="delete-notif" title="Delete Notification"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-data">No new orders</div>
                <?php endif; ?>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="admin_dashboard.php" class="filter-form">
                <div>
                    <label for="search">Search:</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name or email">
                </div>

                <div>
                    <label for="status">Status:</label>
                    <select id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="Pending" <?php echo $statusFilter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Processing" <?php echo $statusFilter === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="Shipped" <?php echo $statusFilter === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                        <option value="Completed" <?php echo $statusFilter === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="Cancelled" <?php echo $statusFilter === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>

                <div>
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($dateFilter); ?>">
                </div>

                <div>
                    <input type="submit" value="Filter" style="margin-top: 1.7rem;">
                </div>
            </form>

            <!-- Orders Table -->
            <section id="ordersTable">
                <h2>Orders</h2>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Zip Code</th>
                                <th>Country</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders): ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                                        <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['email']); ?></td>
                                        <td><?php echo htmlspecialchars($order['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($order['address']); ?></td>
                                        <td><?php echo htmlspecialchars($order['city']); ?></td>
                                        <td><?php echo htmlspecialchars($order['state']); ?></td>
                                        <td><?php echo htmlspecialchars($order['zip_code']); ?></td>
                                        <td><?php echo htmlspecialchars($order['country']); ?></td>
                                        <td>₹<?php echo number_format($order['total_price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                                        <td><?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($order['created_at']))); ?></td>
                                        <td class="actions">
                                            <a href="view_order.php?id=<?php echo htmlspecialchars($order['id']); ?>" title="View Order"><i class="fas fa-eye"></i> View</a>
                                            <a href="update_order.php?id=<?php echo htmlspecialchars($order['id']); ?>" class="update" title="Update Status"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="delete_order.php?id=<?php echo htmlspecialchars($order['id']); ?>" class="delete" title="Delete Order"><i class="fas fa-trash-alt"></i> Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="13" class="no-data">No orders found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <br>

                <!-- User Management Section -->
                <h2>Users</h2>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($users): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($user['created_at']))); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="no-data">No users found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <br>

                <!-- User Form Submissions Section -->
                <section id="userFormsTable">
                    <h2>User Form Submissions</h2>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Email</th>
                                    <th>Message</th>
                                    <th>Submitted At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($userForms): ?>
                                    <?php foreach ($userForms as $form): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($form['id']); ?></td>
                                            <td><?php echo htmlspecialchars($form['email']); ?></td>
                                            <td><?php echo htmlspecialchars($form['message']); ?></td>
                                            <td><?php echo htmlspecialchars($form['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="no-data">No form submissions found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function showUsersTable() {
            document.getElementById('usersTable').style.display = 'block';
            document.getElementById('ordersTable').style.display = 'none';
            document.getElementById('userFormsTable').style.display = 'none';
        }

        function showOrdersTable() {
            document.getElementById('usersTable').style.display = 'none';
            document.getElementById('userFormsTable').style.display = 'none';
            document.getElementById('ordersTable').style.display = 'block';
        }

        function showUserFormsTable() {
            document.getElementById('usersTable').style.display = 'none';
            document.getElementById('ordersTable').style.display = 'none';
            document.getElementById('userFormsTable').style.display = 'block';
        }

        function toggleSidebar() {
            var sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('visible')) {
                sidebar.classList.remove('visible');
            } else {
                sidebar.classList.add('visible');
            }
        }
    </script>
</body>
</html>