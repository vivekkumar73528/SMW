<?php
session_start();
include 'db.php';

$userId = $_SESSION['user_id'] ?? null;
if ($userId === null) {
    header('Location: index.php');
    exit();
}

// Fetch cart items
$stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if cart is empty
if (empty($cartItems)) {
    $errorMessage = "Your cart is empty. Please add items to your cart before proceeding to checkout.";
}

// Initialize total price
$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['item_price'] * $item['quantity'];
}

// Store phone number from GET parameter
$phoneNumber = isset($_GET['phone']) ? $_GET['phone'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout</title>
  <link rel="stylesheet" href="vivo.css">
  <link rel="stylesheet" href="./checkout.css">
  </head>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Roboto", sans-serif;
      font-weight: 300;
      font-style: normal;
    }

    body {}

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      /* background-color: #333; */
      padding: 10px 20px;
    }

    .logo1 img {
      width: 130px;
    }

    .nav-Links {
      list-style: none;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .nav-links {
      list-style: none;
      padding: 0;
      margin: 0;
      display: flex;
      align-items: center;
    }

    .nav-links li {
      margin: 0 15px;
      position: relative;
    }

    .nav-links a {
      color: #000;
      text-decoration: none;
      padding: 5px 10px;
      /* display: block; */
      position: relative;
      transition: color 0.3s ease;
    }

    .nav-links a::after {
      content: "";
      display: block;
      width: 0;
      height: 2px;
      background: #000;
      transition: width 0.3s ease;
      position: absolute;
      left: 0;
      bottom: -5px;
    }

    .nav-links li a:hover::after {
      width: 80%;
    }

    .dropdown a {
      font-size: 18px;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      top: 18px;
      left: 0;
      min-width: 170px;
      z-index: 1;
      background-color: #fefefe;
      padding: 10px 0;
      margin-top: 14px;
    }

    .dropdown-content li {
      list-style: none;
      text-align: left;
    }

    .dropdown-content li a {
      color: #000;
      display: block;
      width: 100%;
      padding: 8px 16px;
      text-align: left;
      text-decoration: none;
      position: relative;
    }

    .dropdown-content li a::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      width: 0;
      height: 2px;
      background-color: #000;
      transition: width 0.4s ease;
    }

    .dropdown-content li a:hover::after {
      width: 40%;
    }

    .dropdown:hover .dropdown-content {
      display: block;
    }


    /* Icon styles */
    .icons {
      display: flex;
      align-items: center;
    }

    .icon {
      width: 25px;
      height: 25px;
      margin-left: 20px;
      cursor: pointer;
    }


    .menu-icon {
      display: none;
      cursor: pointer;
    }



    .menu-icon span {
      display: block;
      width: 25px;
      height: 3px;
      margin: 5px auto;
      background-color: #333;
      transition: all 0.3s ease-in-out;
    }

    .menu-icon.cross span:nth-child(1) {
      transform: rotate(46deg) translate(6px, 6px);
    }

    .menu-icon.cross span:nth-child(2) {
      opacity: 0;
    }

    .menu-icon.cross span:nth-child(3) {
      transform: rotate(-46deg) translate(5px, -5px);
    }


    .hr {
      text-align: center;
      /* Centers the <hr> */
      margin: 20px 0;
      /* Adjust the space above and below the <hr> */
    }

    .hr hr {
      border: 0;
      /* Removes the default border */
      height: 1px;
      /* Sets the height to a small value */
      background: #ddd;
      /* Light gray background color */
      width: 100%;
      /* Sets the width of the <hr> */
      margin: 0 auto;
      /* Centers the <hr> horizontally */
    }


    /* ---------------------
         About
  -----------------------*/

    .site-box-container {
      padding: 20px;
      width: 100%;
    }

    .container {
      width: 100%;
      /* max-width: 800px; */
      margin: 0 auto;
    }

    .name h1 {
      margin-left: 150px;
      /* text-align: left; */
      position: relative;
      font-size: 2em;
      margin-top: 40px;
      font-size: 60px;
    }

    .name h1::after {
      content: '';
      display: block;
      width: 100px;
      height: 4px;
      background-color: #000;
      position: absolute;
      bottom: -5px;
      left: 0;
      margin-right: 4px;
    }

    .rta p {
      margin-left: 70px;
      /* text-align: left; */
      margin-top: 40px;
      line-height: 1.5;
      font-size: 1.1em;
    }



    /* ---------------------
       FOOTER
  ----------------------*/


    .footer {
      background-color: rgba(0, 0, 0, 0.18);
      color: #000;
      padding: 40px 20px;
      /* Adjust padding as needed */
      /* font-size: 24px; */
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      margin-top: 20px;
    }


    .column {
      padding: 10px;
      cursor: pointer;
    }

    .column h1 {
      font-size: 24px;
      margin-bottom: 10px;
    }

    .column h2 {
      font-size: 16px;
      margin: 5px 0;
      cursor: pointer;
    }


    .social-icons {
      display: flex;
      flex-direction: column;
    }

    .social-icons a {
      display: flex;
      align-items: center;
      /* Center items vertically */
      text-decoration: none;
      /* Remove underline from links */
      /* color: #333;  */
      margin-bottom: 4px;
      /* Optional: Space between icons */
      font-size: 16px;
    }

    .social-icons img {
      width: 20px;
      /* Adjust size as needed */
      /* height: 10px;  */
      object-fit: cover;
      /* Maintain aspect ratio */
      margin-right: 10px;
    }


    .footer a {
      color: #000;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .footer .column h2:hover {
      color: #94bbe9;
    }

    .footer-content {
      display: flex;
      justify-content: center;
      /* Center-aligns horizontally */
      align-items: center;
      /* Center-aligns vertically */
      height: 100px;
      /* Adjust height as needed */
    }

    .footer-content p {
      margin: 0;
      /* Remove default margin */
      font-size: 14px;
      /* Font size */
      color: #fff;
      /* Text color */
    }


    .Copyright {
      display: flex;
      justify-content: center;
      /* Center-aligns horizontally */
      align-items: center;
      /* Center-aligns vertically */
      height: 100px;
      /* Adjust height as needed */
      background-color: rgba(0, 0, 0, 0.18);
      color: #000;
      /* Text color */
      padding: 20px 0;
      /* Padding for top and bottom */
    }

    .Copyright p {
      margin: 0;
      /* Remove default margin */
      font-size: 14px;
      /* Font size */
    }

    /* Media Query for responsive design */
    /* Add this at the bottom of the existing CSS */
    .dropdown.show .dropdown-content {
      display: flex;
    }


    /* -----------------------------
         RESPONSIVE
  --------------------------------*/

    @media (max-width: 768px) {
      .nav-Links {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 80px;
        left: 0;
        width: 100%;
        z-index: 1;
        height: 4%;
        margin-left: 0;
        margin-top: 130px;
        align-items: flex-start;
        color: inherit;
      }


      .nav-Links.active {
        display: flex;
      }

      .dropdown {
        width: 50%;
      }

      .dropdown-content {
        position: static;
        box-shadow: none;
      }

      .dropdown-content a {
        padding: 3px 50px;
        width: 20px;
      }

      .nav-links {
        flex-direction: column;
        align-items: flex-start;
        background-color: #f9f9f9;
        margin-top: 340px;
        width: 100%;
        min-height: 450px;
        margin-left: 0px;
      }

      .nav-Links a {
        text-align: left;
        justify-content: flex-start;
        width: 100%;
        max-width: 450px;
      }

      .nav-Links li {
        margin: 10px 0;
        text-transform: uppercase;
        text-align: left;
        width: 50%;
        margin-top: 5px;

      }

      .nav-Links.show {
        display: flex;
      }

      .icons {
        padding: 5px;
        margin-left: 5px;
      }

      .menu-icon {
        display: block;
        padding: 5px;
        margin-left: 14px;
      }

      .item-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        /* Adjust the gap between items */
        padding: 10px;
        /* Adjust the padding around the grid */
        /* max-width: 200px; */
      }

      .footer {
        grid-template-columns: repeat(1, 1fr);
      }
    }


    @media (max-width: 600px) {
      .nav-Links {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 80px;
        left: 0;
        width: 100%;
        z-index: 1;
        height: 4%;
        margin-left: 0;
        margin-top: 130px;
        align-items: flex-start;
        color: inherit;
      }


      .nav-Links.active {
        display: flex;
      }

      .dropdown {
        width: 50%;
      }

      .dropdown-content {
        position: static;
        box-shadow: none;
      }

      .dropdown-content a {
        padding: 3px 50px;
        width: 20px;
      }

      .nav-links {
        flex-direction: column;
        align-items: flex-start;
        background-color: #f9f9f9;
        margin-top: 340px;
        width: 100%;
        min-height: 450px;
        margin-left: 0px;
      }

      .nav-Links a {
        text-align: left;
        justify-content: flex-start;
        width: 100%;
        max-width: 450px;
      }

      .nav-Links li {
        margin: 10px 0;
        text-transform: uppercase;
        text-align: left;
        width: 50%;
        margin-top: 5px;

      }

      .nav-Links.show {
        display: flex;
      }

      .icons {
        padding: 5px;
        margin-left: 5px;
      }

      .menu-icon {
        display: block;
        padding: 5px;
        margin-left: 14px;
      }

      .item-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        /* Adjust the gap between items */
        padding: 10px;
        /* Adjust the padding around the grid */
        /* max-width: 200px; */
      }

      .footer {
        grid-template-columns: repeat(1, 1fr);
      }

    }

    @media (max-width: 480px) {
      .nav-Links {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 80px;
        left: 0;
        width: 100%;
        z-index: 1;
        height: 4%;
        margin-left: 0;
        margin-top: 130px;
        align-items: flex-start;
        color: inherit;
      }

      .item {
        width: 200px;
        grid-template-columns: 1fr;
        /* 1 column for very small screens */
      }
    }
  </style>








<body>
  <!-- ------------------------------Header--------------------------- -->

  <nav class="navbar">
    <div class="logo1">
      <a href="../SMW/Phone Skin/Home.html"><img src="../SMW/Phone Skin/Assets/LOGO.png" alt="Logo"></a>
    </div>
    <div class="nav-Links">
      <ul class="nav-links">
        <li class="dropdown">
          <a href="../SMW/Phone Skin/Mobile Skins/Mobile skin.html">Mobile Skins</a>
          <ul class="dropdown-content">
            <li><a href="../SMW/Phone Skin/Mobile Skins/iPhone/iPhone.html">iPhone</a></li>
            <li><a href="../SMW/Phone Skin/Mobile Skins/Samsung/Samsung.html">Samsung</a></li>
            <li><a href="../SMW/Phone Skin/Mobile Skins/Oneplus/Oneplus.html">Oneplus</a></li>
            <li><a href="../SMW.Phone Skin/Mobile Skins/Tecno/Tecno.html">Tecno</a></li>
            <li><a href="../SMW/Phone Skin/Mobile Skins/Asus/Asus.html">Asus</a></li>
            <li><a href="../SMW/Phone Skin/Mobile Skins/realme/realme.html">realme</a></li>
            <li><a href="../SMW/Phone Skin/Mobile Skins/OPPO/OPPO.html">OPPO</a></li>
            <li><a href="../SMW/Phone Skin/Mobile Skins/Vivo/Vivo.html">Vivo</a></li>
            <li><a href="../SMW/Phone Skin/Mobile Skins/POCO/POCO.html">POCO</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#">Skin Collections</a>
          <ul class="dropdown-content">
            <li><a href="../SMW/Phone Skin/Skins Collection/Dark/Dark.html">Dark</a></li>
            <li><a href="../SMW/Phone Skin/Skins Collection/Cyberforce/Cyberforce.html">Cyberforce</a></li>
            <li><a href="../SMW/Phone Skin/Skins Collection/Wanderlust/Wanderlust.html">Wanderlust</a></li>
            <li><a href="../SMW/Phone Skin/Skins Collection/Retro Funk/Retro.html">Retro</a></li>
            <li><a href="../SMW/Phone Skin/Skins Collection/Design Archive/Archive.html">Archive</a></li>
          </ul>
        </li>
        <li class="dropdown"><a href="../SMW/Phone Skin/Apply/Applye.html">How To Apply</a></li>
        <li class="dropdown"><a href="../SMW/login.php">Login</a></li>
      </ul>
      </li>
    </div>
    <div class="icons">
      <img src="../SMW/Phone Skin/Assets/person-outline.svg" alt="User Icon" class="icon">
      <a href="/SMW/cart.php"><img src="../SMW/Phone Skin/Assets/cart.svg" alt="Cart Icon" class="icon"></a>
      <div class="menu-icon">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </nav>
  <!-- -------------------------MIDDLE-------------- -->

  <div class="hr">
    <hr>
  </div>
  <div class="container">
    <h1>Checkout</h1>

    <?php if (isset($errorMessage)) : ?>
    <p class="error-message">
      <?php echo htmlspecialchars($errorMessage); ?>
    </p>
    <?php else : ?>
    <form action="order_confirmation.php" method="POST" onsubmit="handleSubmit(event)">
      <h2>Shipping Information</h2>
      <label for="full_name">Full Name:</label>
      <input type="text" id="full_name" name="full_name" required>
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>
      <label for="phone">Phone Number:</label>
      <input type="text" id="phone" name="phone" pattern="^[6-9]\d{9}$" value="<?= htmlspecialchars($phoneNumber) ?>"
        required>
      <label for="address">Address:</label>
      <input type="text" id="address" name="address" required>
      <label for="city">City:</label>
      <input type="text" id="city" name="city" required readonly placeholder="Enter ZIP Code">
      <label for="state">State:</label>
      <input type="text" id="state" name="state" required readonly placeholder="Enter ZIP Code">
      <label for="zip_code">Zip Code:</label>
      <input type="text" id="zip_code" name="zip_code" required>
      <div id="zip_error" class="error-message"></div>
      <label for="country">Country:</label>
      <input type="text" id="country" name="country" value="India" required>

      <h2>Payment Method</h2>
      <div class="radio-group">
        <label>
          <input type="radio" name="payment_method" value="online" checked onclick="updateTotalPrice()"> Online Payment
        </label>
        <label>
          <input type="radio" name="payment_method" value="cod" onclick="updateTotalPrice()"> Cash on Delivery (COD)
        </label>
      </div>

      <input type="submit" value="Place Order">
    </form>

    <h2>Your Cart Items</h2>
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
        <?php foreach ($cartItems as $item) : ?>
        <tr>
          <td>
            <?php echo htmlspecialchars($item['item_name']); ?>
          </td>
          <td>₹
            <?php echo number_format($item['item_price'], 2); ?>
          </td>
          <td>
            <?php echo htmlspecialchars($item['quantity']); ?>
          </td>
          <td>₹
            <?php echo number_format($item['item_price'] * $item['quantity'], 2); ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p class="total-price">Total Price: <span id="total-price1">₹
        <?php echo number_format($totalPrice, 2); ?>
      </span></p>
    <?php endif; ?>
  </div>

  <!-- ----------------------------FOOTER----------------------------------  -->

  <footer class="footer">
    <div class="column">
      <h1>Vision</h1>
      <h2>Creativity, Expression, & Exploration</h2>
    </div>
    <div class="column">
      <h1>Pages</h1>
      <a href="../SMW/Phone Skin/About/About.html">
        <h2>About Us</h2>
      </a>
      <h2>Contact Us</h2>
      <a href="../SMW/Phone Skin/Condition/index.html">
        <h2>Terms and Conditions</h2>
      </a>
      <a href="../SMW/Phone Skin/Policy/index.html">
        <h2>Privacy Policy</h2>
      </a>
      <h2>Refund/Cancellations, Shipping Policy</h2>
      <h2>FAQ</h2>
    </div>
    <div class="column">
      <h1>My Accounts</h1>
      <a href="login.php">
        <h2>Login</h2>
      </a>
      <a href="register.php">
        <h2>Register</h2>
      </a>
      <h2>Track Order</h2>
    </div>
    <div class="column">
      <h1>Follow Us</h1>
      <div class="social-icons">
        <a href="#"><img src="../SMW/Phone Skin/Assets/twit.png" alt="Twitter">Twitter</a>
        <a href="#"><img src="../SMW/Phone Skin/Assets/fbook.png" alt="Facebook">Facebook</a>
        <a href="#"><img src="../SMW/Phone Skin/Assets/ytube.png" alt="YouTube">YouTube</a>
        <a href="#"><img src="../SMW/Phone Skin/Assets/inst.png" alt="Instagram">Instagram</a>
      </div>
    </div>
  </footer>
  <hr>
  <div class="Copyright">
    <p>&copy; 2024 Your Company Name. All rights reserved.</p>
  </div>
  <!-- <script src="checkout.js"></script> -->

  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>


    // -----------------MENU ICON -------------------------

    const menuIcon = document.querySelector('.menu-icon');
    const navLinks = document.querySelector('.nav-Links');

    menuIcon.addEventListener('click', () => {
      menuIcon.classList.toggle('cross');
      navLinks.classList.toggle('show');
    });


    document.addEventListener('DOMContentLoaded', () => {
      const logoSection = document.querySelector('.logo-section');
      const logos = document.querySelector('.logos');

      let scrollInterval;
      const scrollAmount = 10; // Adjust this value to control scroll speed
      const scrollDelay = 20; // Time in milliseconds between scrolls

      logoSection.addEventListener('mousemove', (e) => {
        const sectionWidth = logoSection.offsetWidth;
        const xPos = e.clientX - logoSection.getBoundingClientRect().left;

        if (xPos < sectionWidth * 0.1) {
          // Scroll left
          clearInterval(scrollInterval);
          scrollInterval = setInterval(() => {
            logos.scrollLeft -= scrollAmount;
            if (logos.scrollLeft <= 0) clearInterval(scrollInterval);
          }, scrollDelay);
        } else if (xPos > sectionWidth * 0.9) {
          // Scroll right
          clearInterval(scrollInterval);
          scrollInterval = setInterval(() => {
            logos.scrollLeft += scrollAmount;
            if (logos.scrollLeft >= (logos.scrollWidth - logos.clientWidth)) clearInterval(scrollInterval);
          }, scrollDelay);
        } else {
          clearInterval(scrollInterval);
        }
      });

      logoSection.addEventListener('mouseleave', () => {
        clearInterval(scrollInterval);
      });
    });

  </script>
</body>

</html>