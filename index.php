<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Skins</title>
    <link rel="stylesheet" href="vivo.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    
    <style>
        .main {
            position: relative;
            margin: 0 auto;
        }
        header {
            width: 100%;
            height: 50px;
            background-color: brown;
            color: white;
            font-family: monospace;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            padding: 0 20px;
            box-sizing: border-box;
        }
        header a {
            color: white;
            text-decoration: none;
            font-size: 1rem;
        }
        .cart-title {
            text-align: center;
            margin-top: 20px;
            font-size: 2rem;
        }
        .item-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .item {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            width: 200px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .item img {
            width: 100%;
            height: auto;
        }
        .item-details {
            padding: 10px;
        }
        .item-name {
            display: block;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        .item-price {
            display: block;
            color: green;
            font-size: 1rem;
            margin-bottom: 10px;
        }
        .button {
            padding: 10px;
        }
        .button button {
            background-color: brown;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px;
            cursor: pointer;
        }
        .button button:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <header>
        <a href="./Admin/admin_login.php">Admin Login</a>

        <a href="./form.php" style="margin-left: 2rem;"  >User Form</a>
        <a href="./user_dashboard.php" style="margin-left: 2rem;"  >User dashboard</a>
      
    </header>
    <h1 class="cart-title">Best Selling Mobile Skins</h1>
    <div class="item-grid">
        <div class="item">
            <img src="./Images/vivo-nex.webp" alt="Samsung Galaxy S24 Ultra">
            <div class="item-details">
                <span class="item-name">Samsung Galaxy S24 Ultra</span>
                <span class="item-price">$25</span>
                <input type="hidden" class="item-image" value="./Images/vivo-nex.webp">
            </div>
            <div class="button">
                <button onclick="addToCart(this)">Add to Cart</button>
            </div>
        </div>
        <div class="item">
            <img src="./Images/Vivo_X50_Pro_Shutterbug.webp" alt="Samsung Galaxy A73">
            <div class="item-details">
                <span class="item-name">Samsung Galaxy A73</span>
                <span class="item-price">$20</span>
                <input type="hidden" class="item-image" value="./Images/Vivo_X50_Pro_Shutterbug.webp">
            </div>
            <div class="button">
                <button onclick="addToCart(this)">Add to Cart</button>
            </div>
        </div>
    </div>

    <script>
        function addToCart(button) {
            var itemContainer = button.closest('.item');
            var itemName = itemContainer.querySelector('.item-name').textContent;
            var itemPrice = parseFloat(itemContainer.querySelector('.item-price').textContent.replace('$', ''));
            var itemImage = itemContainer.querySelector('.item-image').value;

            // Send an AJAX request to add_to_cart.php
            $.ajax({
                url: 'add_to_cart.php',
                type: 'POST',
                data: {
                    itemName: itemName,
                    itemPrice: itemPrice,
                    itemImage: itemImage
                },
                success: function(response) {
                    console.log(response); // Log the response for debugging
                    window.location.href = 'cart.php'; // Redirect to cart page after adding item to cart
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText); // Log any error for debugging
                }
            });
        }
    </script>
</body>
</html>
