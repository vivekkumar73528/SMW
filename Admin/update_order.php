<?php
session_start();
include '../db.php';

// Check if the user is an admin
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

$orderId = $_GET['id'] ?? null;
if (!$orderId) {
    header('Location: admin_dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $orderId]);

    header('Location: admin_dashboard.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order Status</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style> 
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
    color: #3f5a73;
}

.table-wrapper tr:nth-child(even) {
    background-color: #f2f2f2;
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
</style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
   
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="../admin_dashboard.php">Admin Dashboard</a>
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
                    <a class="nav-link" href="#" onclick="showReports()">Sales</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

  
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
        <h1 class="text-2xl font-bold mb-4">Update Order Status</h1>
        <form method="POST" action="update_order.php?id=<?php echo htmlspecialchars($order['id']); ?>" class="bg-white p-6 rounded-lg shadow-lg">
            <div class="mb-4">
                <label for="status" class="block text-gray-700 text-sm font-semibold mb-2">Order Status:</label>
                <select id="status" name="status" class="block w-full bg-gray-200 border border-gray-300 rounded-lg py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500">
                    <option value="Pending" <?php echo $order['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Processing" <?php echo $order['status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="Shipped" <?php echo $order['status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                    <option value="Completed" <?php echo $order['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="Cancelled" <?php echo $order['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300">Update Status</button>
        </form>
    </main>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  
<script>
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
