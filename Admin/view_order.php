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

// Fetch order details including image paths
$query = "
    SELECT 
        o.*, 
        oi.product_name, 
        oi.item_price, 
        oi.quantity,
        oi.item_image
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$orderId]);
$orderDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ensure order exists
if (!$orderDetails) {
    header('Location: admin_dashboard.php');
    exit();
}

$order = $orderDetails[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    
    <link rel="stylesheet" href="styles.css">
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
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .container h1 {
            text-align: center;
            color: orange;
        }

        .container h2 {
            margin-top: 20px;
        }
        
        p {
            margin: 10px 0;
            color: #555;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background-color: #e9ecef;
            color: #495057;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #f1f3f5;
        }
        .price {
            text-align: right;
            color: #333;
        }
        .item-image {
            width: 100px;
            height: auto;
        }
    </style>
</head>
<body>
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

    <aside class="sidebar" id="sidebar">
        <a href="./admin_dashboard.php" onclick="showDashboard()">Dashboard</a>
        <a href="#" onclick="showUsersTable()">Users</a>
        <a href="#" onclick="showOrdersTable()">Orders</a>
        <a href="#" onclick="showUserFormsTable()">User Forms</a>
        <a href="#" onclick="showReports()">Sales</a>
        <a href="admin_logout.php">Logout</a>
    </aside>

    <main class="main-content">
    <div class="container">
        <h1>Order Information</h2>
        <!-- Order details -->
        <h2>Ordered Items</h2>
        <div class="table-wrapper">
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
                <?php foreach ($orderDetails as $item): ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($item['item_image']); ?>" alt="Product Image" class="item-image"></td>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td class="price">₹<?php echo number_format($item['item_price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td class="price">₹<?php echo number_format($item['item_price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </div>
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
