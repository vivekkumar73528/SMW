<?php session_start();
include 'db.php';

// Ensure user is logged in
$userId =$_SESSION['user_id'] ?? null;

if ($userId ===null) {
    header('Location: index.php');
    exit();
}

// Fetch user profile information
$stmt_user =$pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->execute([$userId]);
$user =$stmt_user->fetch(PDO::FETCH_ASSOC);

// Fetch cart items
$stmt_cart =$pdo->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt_cart->execute([$userId]);
$cartItems =$stmt_cart->fetchAll(PDO::FETCH_ASSOC);

// Fetch orders
$stmt_orders =$pdo->prepare("SELECT * FROM orders WHERE user_id = ? AND removed = 0 ORDER BY created_at DESC");
$stmt_orders->execute([$userId]);
$orders =$stmt_orders->fetchAll(PDO::FETCH_ASSOC);

// Handle profile update
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_profile'])) {
    $username =$_POST['username'];
    $email =$_POST['email'];

    $stmt_update =$pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt_update->execute([$username, $email, $userId]);

    // Reload user information
    $stmt_user->execute([$userId]);
    $user =$stmt_user->fetch(PDO::FETCH_ASSOC);
    $_SESSION['message']="Profile updated successfully!";
    header('Location: user_dashboard.php'); // Redirect to avoid form re-submission
    exit();
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['profile_picture'])) {
    $file =$_FILES['profile_picture'];
    $uploadDir ='uploads/';
    $uploadFile =$uploadDir . basename($file['name']);

    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
        $stmt_update_picture =$pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        $stmt_update_picture->execute([$file['name'], $userId]);
        $user['profile_picture']=$file['name'];
        $_SESSION['message']="Profile picture updated successfully!";
    }

    else {
        $_SESSION['message']="Failed to upload profile picture.";
    }

    header('Location: user_dashboard.php'); // Redirect to avoid form re-submission
    exit();
}

// Handle cart update
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_cart'])) {
    $cartUpdates =$_POST['cart'];

    foreach ($cartUpdates as $itemId => $quantity) {
        $stmt_update_cart =$pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt_update_cart->execute([$quantity, $itemId, $userId]);
    }

    $_SESSION['message']="Cart updated successfully!";
    header('Location: user_dashboard.php'); // Redirect to avoid form re-submission
    exit();
}

// Handle order removal
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['remove_order'])) {
    $orderId =$_POST['order_id'];

    // Prepare and execute the statement to mark the order as removed
    $stmt_remove_order =$pdo->prepare("UPDATE orders SET removed = 1 WHERE id = ? AND user_id = ?");
    $stmt_remove_order->execute([$orderId, $userId]);

    // Insert a notification
    $stmt_insert_notification =$pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt_insert_notification->execute([$userId, "Your order #$orderId has been removed."]);

    $_SESSION['message']="Order removed successfully!";
    header('Location: user_dashboard.php'); // Redirect to avoid form re-submission
    exit();
}

// Fetch any message from session
$message =$_SESSION['message'] ?? '';
unset($_SESSION['message']);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<style>
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f4f5f7;
    }

    .navbar {
        background-color: #333;
        padding: 1rem 2rem;
    }

    .navbar-brand {
        font-weight: bold;
        font-size: 1.5rem;
    }

    .navbar-nav .nav-link {
        color: #ffffff;
        font-size: 1rem;
    }

    .navbar-nav .nav-link:hover {
        color: #f8f9fa;
    }

    .navbar-nav .nav-link.active {
        background-color: #444;
        border-radius: 4px;
    }

    .container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 30px;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .card-header {
        background-color: #007bff;
        color: #ffffff;
        font-size: 1.25rem;
        border-radius: 8px 8px 0 0;
        padding: 0.75rem 1.25rem;
    }

    .card-body {
        background-color: #ffffff;
        padding: 1.25rem;
    }

    .profile-picture {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #007bff;
        margin-top: 1rem;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
        border-radius: 4px;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
        border-radius: 4px;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .btn-success {
        background-color: #28a745;
        border: none;
        border-radius: 4px;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    table th,
    table td {
        padding: 12px;
        border: 1px solid #dee2e6;
        text-align: left;
    }

    table th {
        background-color: #007bff;
        color: #ffffff;
    }

    table tbody tr:nth-of-type(even) {
        background-color: #f9f9f9;
    }

    .message {
        color: #28a745;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .message.error {
        color: #dc3545;
    }

    .navbar-nav .nav-item .nav-link {
        position: relative;
    }

    .navbar-nav .nav-item .nav-link .badge {
        position: absolute;
        top: -10px;
        right: -10px;
        padding: 5px 10px;
        font-size: 12px;
    }

    @media (max-width: 767px) {
        .container {
            padding: 15px;
        }

        .profile-picture {
            width: 100px;
            height: 100px;
        }

        .card-header {
            font-size: 1.1rem;
        }

        table th,
        table td {
            font-size: 0.875rem;
            padding: 8px;
        }
    }

    @media (max-width: 575px) {
        .card-header {
            font-size: 1rem;
        }

        .btn {
            width: 100%;
            margin-bottom: 10px;
        }

        .navbar-nav .nav-link {
            font-size: 0.875rem;
        }
    }



    .profile-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        width: 100%;
        padding: 20px;
        text-align: center;
    }

    .profile-header {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }

    .profile-pic {
        border-radius: 50%;
        width: 120px;
        height: 120px;
        object-fit: cover;
        margin-right: 20px;
    }

    .profile-info {
        text-align: left;
    }

    .edit-btn,
    .submit-btn {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 10px;
        font-size: 16px;
    }

    .edit-btn:hover,
    .submit-btn:hover {
        background-color: #0056b3;
    }

    .profile-form {
        display: none;
        text-align: left;
    }

    .profile-form form {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .profile-form input {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
    }
</style>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark"><a class="navbar-brand" href="#">E-Commerce</a><button
            class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"><span
                class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="user_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                <li class="nav-item"><a class="nav-link" href="checkout.php">Checkout</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <li class="nav-item"><a class="nav-link" href="return_item.php">Return Item</a></li>
                <li class="nav-item"><a class="nav-link" href="./feedback_form.php">Leave Feedback</a></li>
                <li class="nav-item"><a class="nav-link" href="./contact_support.php">Contact Support</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <!-- Display message -->
        <?php if ($message) : ?>
        <p class="message">
            <?php echo htmlspecialchars($message);
?>
        </p>
        <?php endif;
?>
        <h1>User Dashboard</h1>
        <Profile Information>
        <div class="card">
            <div class="card-header">Profile Information </div>
            <div class="card-body">
                <form action="user_dashboard.php" method="POST">
                    <div class="form-group"><label for="username">Username:</label><input type="text" id="username"
                            name="username" class="form-control"
                            value="<?php echo htmlspecialchars($user['username']); ?>" required></div>
                    <div class="form-group"><label for="email">Email:</label><input type="email" id="email" name="email"
                            class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required></div>
                    <input type="submit" name="update_profile" value="Update Profile" class="btn btn-primary">
                </form>
                <form action="user_dashboard.php" method="POST" enctype="multipart/form-data" class="mt-4">
                    <div class="form-group"><label for="profile_picture">Upload Profile
                            Picture:</label><input type="file" id="profile_picture" name="profile_picture"
                            class="form-control-file"></div><input type="submit" name="update_picture"
                        value="Upload Picture" class="btn btn-primary">
                </form>
                <?php if ( !empty($user['profile_picture'])) : ?><img
                    src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture"
                    class="profile-picture mt-4">
                <?php endif;
?>
        </div>
    </div>
    <div class="profile-container">
        <h1>User Profile</h1>
        <div class="profile-header"><label for="file-input" class="profile-pic-label"><img id="profile-pic"
                    src="default-pic.jpg" alt="Profile Picture" class="profile-pic"></label><input type="file"
                id="file-input" accept="image/*" style="display: none;">
            <div class="profile-info">
                <h2 id="username">John Doe</h2>
                <p id="email">john.doe@example.com</p>
            </div>
        </div><button id="edit-profile" class="edit-btn">Edit Profile</button>
        <div id="profile-form" class="profile-form">
            <h3>Edit Profile</h3>
            <form id="form"><label for="username-input">Username:</label><input type="text" id="username-input"
                    placeholder="Enter username"><label for="email-input">Email:</label><input type="email"
                    id="email-input" placeholder="Enter email"><button type="submit" class="submit-btn">Save
                    Changes</button></form>
        </div>
    </div>
    <!-- Cart Items -->
    <div class="card">
        <div class="card-header">Shopping Cart </div>
        <div class="card-body">
            <?php if ( !empty($cartItems)) : ?>
            <form action="user_dashboard.php" method="POST">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item) : ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($item['item_name']);
?>
                                </td>
                                <td>₹
                                    <?php echo number_format($item['item_price'], 2);
?>
                                </td>
                                <td><input type="number" name="cart[<?php echo htmlspecialchars($item['id']); ?>]"
                                        value="<?php echo htmlspecialchars($item['quantity']); ?>" class="form-control"
                                        min="1"></td>
                                <td>₹
                                    <?php echo number_format($item['item_price'] * $item['quantity'], 2);
?>
                                </td>
                            </tr>
                            <?php endforeach;
?>
                        </tbody>
                    </table>
                </div><input type="submit" name="update_cart" value="Update Cart" class="btn btn-primary">
            </form><a href="checkout.php" class="btn btn-success mt-3">Proceed to Checkout</a>
            <?php else : ?>
            <p>Your cart is empty.</p>
            <?php endif;
?>
        </div>
    </div>
    <!-- Order History -->
    <div class="card">
        <div class="card-header">Order History </div>
        <div class="card-body">
            <?php if ( !empty($orders)) : ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total Price</th>
                            <th>Details</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order) : ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($order['id']);
?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars(date('F j, Y', strtotime($order['created_at'])));
?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars(ucfirst($order['status']));
?>
                            </td>
                            <td>₹
                                <?php echo number_format($order['total_price'], 2);
?>
                            </td>
                            <td><a href="order_details.php?id=<?php echo htmlspecialchars($order['id']); ?>"
                                    class="btn btn-info btn-sm">View Details</a></td>
                            <td>
                                <form action="user_dashboard.php" method="POST" style="display:inline;"
                                    onsubmit="return confirmCancel();"><input type="hidden" name="order_id"
                                        value="<?php echo htmlspecialchars($order['id']); ?>"><input type="submit"
                                        name="remove_order" value="Remove" class="btn btn-danger btn-sm"></form>
                            </td>
                        </tr>
                        <?php endforeach;
?>
                    </tbody>
                </table>
            </div>
            <?php else : ?>
            <p>You have no orders yet.</p><a href="./index.php" class="btn btn-primary">Go to Home Page</a>
            <?php endif;

?>
        </div>
    </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>function confirmCancel() {
            return confirm("Are you sure you want to cancel this order?");
        }

        document.addEventListener('DOMContentLoaded', () => {
            const editButton = document.getElementById('edit-profile');
            const profileForm = document.getElementById('profile-form');
            const form = document.getElementById('form');
            const profilePic = document.getElementById('profile-pic');
            const usernameElem = document.getElementById('username');
            const emailElem = document.getElementById('email');
            const fileInput = document.getElementById('file-input');

            // Toggle profile form visibility
            editButton.addEventListener('click', () => {
                profileForm.style.display = (profileForm.style.display === 'none' || profileForm.style.display === '') ? 'block' : 'none';
            });

            // Handle form submission
            form.addEventListener('submit', (event) => {
                event.preventDefault();

                const username = document.getElementById('username-input').value;
                const email = document.getElementById('email-input').value;

                if (username) usernameElem.textContent = username;
                if (email) emailElem.textContent = email;

                profileForm.style.display = 'none';
            });

            // Open file input on profile picture click
            profilePic.addEventListener('click', () => {
                fileInput.click();
            });

            // Handle file input change
            fileInput.addEventListener('change', () => {
                const file = fileInput.files[0];

                if (file) {
                    const reader = new FileReader();

                    reader.onload = (e) => {
                        profilePic.src = e.target.result;
                    }

                        ;
                    reader.readAsDataURL(file);
                    fileInput.value = ''; // Clear file input value
                }
            });
        });

    </script>
</body>

</html>