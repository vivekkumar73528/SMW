<?php session_start();
include 'db.php'; // Ensure this file has the correct PDO connection setup

$userId =$_SESSION['user_id'] ?? null;

if ($userId ===null) {
    header('Location: index.php');
    exit();
}

$message =''; // Initialize message variable

// Handle return request
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['request_return'])) {
    $orderId =$_POST['order_id'];
    $productName =$_POST['product_name'];
    $reason =$_POST['reason'];

    try {
        // Insert return request into the returns table
        $stmt =$pdo->prepare("INSERT INTO returns (user_id, order_id, product_name, reason, requested_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$userId, $orderId, $productName, $reason]);

        // Redirect to success page
        header('Location: return_success.php');
        exit();
    }

    catch (Exception $e) {
        // Handle errors and set error message
        $message ="Error: " . $e->getMessage();
    }
}

// Fetch completed orders with items no older than 1 week
$oneWeekAgo =date('Y-m-d H:i:s', strtotime('-1 week'));

$stmt =$pdo->prepare("
 SELECT o.id AS order_id, o.created_at AS order_date, o.total_price,
    oi.product_name, oi.item_image, oi.item_price, oi.quantity FROM orders o JOIN order_items oi ON o.id=oi.order_id WHERE o.user_id=? AND o.status='completed' AND o.created_at >=? ");
$stmt->execute([$userId, $oneWeekAgo]);
    $orders =$stmt->fetchAll(PDO::FETCH_ASSOC);

    ?>
<!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Request a Return</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    </head>
        <style>
            /* General reset and box-sizing */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: "Poppins", sans-serif;
                font-weight: 600;
                font-style: normal;
            }

            /* Body styling */
            body {
                width: 100%;
                background-color: #f8f9fa;
            }

            /* Container styling */
            .container {
                margin-top: 10px;
                padding: 20px;
                width: 80%;
                /* Adjust the width as needed */
                margin: 20px auto;
                /* Center the container */
            }

            .mb-4 {
                text-align: center;
                margin-bottom: 20px;
            }

            .card-info h6 {
                font-size: 20px;
            }

            .order-card {
                margin-bottom: 20px;
                padding: 15px;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                background-color: #ffffff;
            }

            .order-card h5 {
                font-size: 18px;
                margin-top: 10px;
                margin-bottom: 10px;
            }

            .order-card img {
                width: 100%;
                height: auto;
                max-width: 150px;
                border-radius: 8px;
            }

            .product-card {
                display: flex;
                align-items: flex-start;
                /* Align items to the start of the container */
                padding: 20px;
                border: 1px solid #ccc;
                margin-bottom: 20px;
            }

            .product-image {
                width: 150px;
                /* Set the width of the image */
                height: auto;
                margin-right: 20px;
                /* Space between image and the card-info */
            }

            .card-info {
                flex: 1;
                /* Take up remaining space */
            }

            .card-info h6 {
                font-size: 1.25rem;
                margin-bottom: 10px;
            }

            .card-info p {
                margin: 5px 0;
            }

            .form-group {
                margin-bottom: 15px;
            }

            .return-form label {
                display: block;
                /* margin-bottom: 5px; */
            }

            .return-form textarea {
                width: calc(100% - 40px);
                /* Adjust width to account for padding */
                height: 150px;
                /* Increased height */
                padding: 20px;
                /* Increased padding for better user experience */
                border: 2px solid #888;
                /* Darker border for better visibility */
                border-radius: 10px;
                /* More rounded corners */
                margin-top: 40px;
                /* Adjusted margin top */
                margin-right: 20%;
                /* Reduced margin right for better alignment */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                /* Added subtle shadow */
                font-size: 16px;
                /* Increased font size for readability */
                font-family: Arial, sans-serif;
                /* Changed font family */
                resize: vertical;
                /* Allow vertical resizing only */
                background-color: #f9f9f9;
                /* Slight background color */
                transition: border-color 0.3s ease;
                /* Smooth transition for border color */
            }

            .return-form textarea:focus {
                border-color: #555;
                /* Darker border color on focus */
                outline: none;
                /* Remove default outline */
            }

            #Re {
                /* font-weight: bold; */
                font-size: 17px;
            }


            .btn-custom {
                background-color: #007bff;
                color: #fff;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            .btn-custom:hover {
                background-color: #0056b3;
            }

            .order-card .card-body {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
                align-items: flex-start;
            }




            .alert {
                margin-top: 20px;
            }


            @media (max-width: 768px) {
                .order-card .card-body {
                    flex-direction: column;
                    align-items: stretch;
                }

                .order-card img {
                    max-width: 100%;
                    margin-bottom: 15px;
                }
            }
        </style>

    <body>
        <div class="container">
            <h1 class="mb-4">Request a Return</h1>
            <!-- Display message -->
                <?php if ( !empty($message)): ?>
                <div class="alert alert-info">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <?php endif; ?>
                <?php if ($orders): ?>
                <!-- Display completed orders -->
                    <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <h4>Order ID:
                            <?php echo htmlspecialchars($order['order_id']); ?>
                        </h4>
                        <p><strong>Date:</strong>
                            <?php echo htmlspecialchars($order['order_date']); ?>
                        </p>
                        <p><strong>Total Price:</strong> $
                            <?php echo number_format($order['total_price'], 2); ?>
                        </p>
                        <h5>Items in this Order:</h5>
                        <div class="card-body"> <img src="<?php echo htmlspecialchars($order['item_image']); ?>"
                                alt="Product Image">
                            <div class="card-info">
                                <h6>
                                    <?php echo htmlspecialchars($order['product_name']); ?>
                                </h6>
                                <p><strong>Price:</strong> $
                                    <?php echo number_format($order['item_price'], 2); ?>
                                </p>
                                <p><strong>Quantity:</strong>
                                    <?php echo htmlspecialchars($order['quantity']); ?>
                                </p>
                            </div>
                        </div>
                        <form action="return_item.php" method="POST" class="return-form"> <input type="hidden"
                                name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>"> <input
                                type="hidden" name="product_name"
                                value="<?php echo htmlspecialchars($order['product_name']); ?>">
                            <div class="form-group"> <label
                                    for="reason-<?php echo htmlspecialchars($order['product_name']); ?>"
                                    style="font-weight: bold; font-size: 34px; margin-top: 10px;">Reason for
                                    Return:</label> <textarea
                                    id="reason-<?php echo htmlspecialchars($order['product_name']); ?>" name="reason"
                                    required></textarea> </div> <button type="submit" name="request_return" id="Re"
                                class="btn btn-custom">Request Return</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <p style="font-size: 20px;">"You haven't purchased any order." </p>
                    <?php endif; ?>
        </div>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>