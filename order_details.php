<?php session_start();
include 'db.php';

// Ensure user is logged in
$userId =$_SESSION['user_id'] ?? null;

if ($userId ===null) {
  header('Location: index.php');
  exit();
}

// Fetch order details
$orderId =$_GET['id'] ?? null;

if ($orderId ===null) {
  header('Location: user_dashboard.php');
  exit();
}

$stmt_order =$pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt_order->execute([$orderId, $userId]);
$order =$stmt_order->fetch(PDO::FETCH_ASSOC);

// Fetch ordered items
$stmt_items =$pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt_items->execute([$orderId]);
$items =$stmt_items->fetchAll(PDO::FETCH_ASSOC);

// Handle order cancellation
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['cancel_order'])) {
  $stmt_cancel =$pdo->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = ? AND user_id = ?");
  $stmt_cancel->execute([$orderId, $userId]);
  header('Location: user_dashboard.php');
  exit();
}

$paymentMethod =$order['payment_method'] ?? 'Online Payment'; // Default value

?>
<!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
        margin-top: 15px;
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


      /*------------------------
            MIDDLE
       --------------------------*/
      .container {
        max-width: 900px;
        margin: 40px auto;
        padding: 20px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);

      }

      h2 {
        color: #333;
      }

      .order-summary,
      .shipping-details {

        margin-bottom: 30px;
      }

      .order-summary h2,
      .shipping-details h2 {
        color: #333;
        margin-bottom: 15px;
      }

      table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
      }

      table th,
      table td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: left;
      }

      table th {
        background-color: #f4f4f4;
      }

      table tr:nth-child(even) {
        background-color: #f9f9f9;
      }

      .total-price {
        font-size: 18px;
        font-weight: bold;
        color: #333;
      }

      .item-image {
        max-width: 100px;
        height: auto;
      }

      .btn-cancel {
        background-color: #dc3545;
        color: #fff;
      }

      .btn-cancel:hover {
        background-color: #c82333;
      }

      .shipping-details h1 {
        text-align: center;
        position: relative;
        font-size: 2.5em;
        margin-top: 10px;
        color: #333;
      }

      .shipping-details h1::after {
        content: '';
        display: block;
        width: 190px;
        height: 2px;
        background-color: #333;
        position: absolute;
        bottom: -5px;
        left: 0;
        margin-left: 330px;
      }

      .shipping-details p strong {
        margin-top: 20px;
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
        font-weight: 10px;
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
        transition: color 0.3s ease, transform 0.3s ease;
        /* Add transition for smooth effect */
      }

      .social-icons a:hover {
        /* color: #007bff;
    transform: scale(1.1); */
      }

      .social-icons img {
        width: 20px;
        /* Adjust size as needed */
        /* height: 10px;  */
        object-fit: cover;
        /* Maintain aspect ratio */
        margin-right: 10px;
        transition: transform 0.3s ease;
        /* Add transition for smooth effect */
      }

      .social-icons img:hover {
        transform: scale(1.1);
        /* Scale up on hover */
      }

      .footer a {
        color: #000;
        text-decoration: none;
        transition: color 0.3s ease;
        /* Add transition for smooth effect */
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

      .hr {
        text-align: center;
        /* Centers the <hr> */
        /* margin: 20px 0; */
        /* Adjust the space above and below the <hr> */
      }

      .hr hr {
        border: 0;
        /* Removes the default border */
        height: 1px;
        /* Sets the height to a small value */
        background: #ddd;
        /* Light gray background color */
        width: 80%;
        /* Sets the width of the <hr> */
        margin: 0 auto;
        /* Centers the <hr> horizontally */
      }

      .hr1 hr {
        border: 0;
        /* Removes the default border */
        height: 1px;
        color: #000;
        /* Sets the height to a small value */
        background: #ddd;
        /* Light gray background color */
        width: 100%;
        /* Sets the width of the <hr> */
        margin: 0 auto;
        /* Centers the <hr> horizontally */

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

        .shipping-details h1 {
          font-size: 2.3em;
        }

        .shipping-details h1::after {
          content: '';
          display: block;
          width: 190px;
          height: 2px;
          background-color: #000;
          position: absolute;
          /* bottom: -5px; */
          left: 0;
          margin-left: 250px;
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

        .shipping-details h1 {
          font-size: 2.1em;
        }

        .shipping-details h1::after {
          content: '';
          display: block;
          width: 190px;
          height: 2px;
          background-color: #000;
          position: absolute;
          /* bottom: -5px; */
          left: 0;
          margin-left: 180px;
        }

        .shipping-details p {
          margin-top: 20px;
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

        .shipping-details h1 {
          font-size: 2.1em;
        }

        .shipping-details h1::after {
          content: '';
          display: block;
          width: 190px;
          height: 2px;
          background-color: #000;
          position: absolute;
          /* bottom: -5px; */
          left: 0;
          margin-left: 110px;
        }

        .ship {
          flex-direction: column;
          /* Stack items vertically */
        }

        .ship img {
          margin-left: 0;
          /* Remove left margin */
          margin-top: 20px;
          /* Add top margin for spacing */
          max-width: 100%;
          /* Make image responsive */
        }

        .ship button {
          margin: 0;
          /* Remove margins */
          margin-bottom: 20px;
          /* Add bottom margin for spacing */
        }

        .container {
          max-width: 100%;
          padding: 10px;
          margin: 20px auto;
          border-radius: 5px;
        }

        h2 {
          font-size: 1.5em;
        }

        .order-summary,
        .shipping-details {
          margin-bottom: 20px;
        }

        table th,
        table td {
          padding: 8px;
          font-size: 14px;
        }

        .item-image {
          max-width: 70px;
        }

        .total-price {
          font-size: 16px;
        }

        .btn-cancel {
          width: 100%;
          padding: 10px;
          font-size: 16px;
        }

        .btn-cancel:hover {
          background-color: #a71d2a;
        }


        .item {
          width: 200px;
          grid-template-columns: 1fr;
          /* 1 column for very small screens */
        }
      }

      /* --------------
         SHIPPING
           IMG
  -----------------*/
      .ship {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: auto;
        /* background: orange; */
        background: linear-gradient(109.6deg, rgb(255, 194, 48) 11.2%, rgb(255, 124, 0) 100.2%);
        padding: 20px;
      }

      .ship img {
        max-width: 400px;
        height: auto;
        margin-left: 20px;
        /* Add some space between the button and the image */
      }

      .ship h1 {
        background-color: #4CAF50;
        /* Green background */
        border: none;
        /* Remove borders */
        color: white;
        /* White text */
        padding: 15px 32px;
        /* Some padding */
        text-align: center;
        /* Centered text */
        text-decoration: none;
        /* Remove underline */
        display: inline-block;
        /* Make the button inline-block */
        font-size: 16px;
        /* Increase font size */
        cursor: pointer;
        /* Pointer/hand icon */
        border-radius: 12px;
        /* Rounded corners */
        transition: background-color 0.3s ease, transform 0.3s ease;
        /* Smooth transition for hover effects */
        margin-right: 20px;
        /* Space between button and image */
      }


      /* Hover effect for the button */
      .ship h1:hover {
        background-color: #45a049;
        /* Darker green */
        transform: scale(1.05);
        /* Slightly larger on hover */
      }
    </style>
  

  <body>
    <!-- ------------------------------Header--------------------------- -->
      <nav class="navbar">
        <div class="logo1"><a href="../SMW/Phone Skin/Home.html"><img src="../SMW/Phone Skin/Assets/LOGO.png"
              alt="Logo"></a></div>
        <div class="nav-Links">
          <ul class="nav-links">
            <li class="dropdown"><a href="../SMW/Phone Skin/Mobile Skins/Mobile skin.html">Mobile Skins</a>
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
            <li class="dropdown"><a href="#">Skin Collections</a>
              <ul class="dropdown-content">
                <li><a href="../SMW/Phone Skin/Skins Collection/Dark/Dark.html">Dark</a></li>
                <li><a href="../SMW/Phone Skin/Skins Collection/Cyberforce/Cyberforce.html">Cyberforce</a></li>
                <li><a href="../SMW/Phone Skin/Skins Collection/Wanderlust/Wanderlust.html">Wanderlust</a></li>
                <li><a href="../SMW/Phone Skin/Skins Collection/Retro Funk/Retro.html">Retro</a></li>
                <li><a href="../SMW/Phone Skin/Skins Collection/Design Archive/Archive.html">Archive</a></li>
              </ul>
            </li>
            <li class="dropdown"><a href="../SMW/Phone Skin/Apply/Applye.html">How To Apply</a></li>
            <li class="dropdown"><a href="/SMW/logout.php">Logout</a></li>
          </ul>
          </li>
        </div>
        <div class="icons"><img src="../SMW/Phone Skin/Assets/person-outline.svg" alt="User Icon" class="icon"><a
            href="/SMW/cart.php"><img src="../SMW/Phone Skin/Assets/cart.svg" alt="Cart Icon" class="icon"></a>
          <div class="menu-icon"><span></span><span></span><span></span></div>
        </div>
      </nav>
      <div class="hr">
        <hr>
      </div>
      <!-- ------------------------------MIDDLE------------------------------- -->
        <div class="container">
          <?php if ($order): ?>
          <div class="shipping-details">
            <h1>Shipping Information</h1>
            <p><strong>Full Name:</strong>
              <?php echo htmlspecialchars($order['full_name']);
?>
            </p>
            <p><strong>Email:</strong>
              <?php echo htmlspecialchars($order['email']);
?>
            </p>
            <p><strong>Phone:</strong>
              <?php echo htmlspecialchars($order['phone']);
?>
            </p>
            <p><strong>Address:</strong>
              <?php echo htmlspecialchars($order['address']);
?>
            </p>
            <p><strong>City:</strong>
              <?php echo htmlspecialchars($order['city']);
?>
            </p>
            <p><strong>State:</strong>
              <?php echo htmlspecialchars($order['state']);
?>
            </p>
            <p><strong>Zip Code:</strong>
              <?php echo htmlspecialchars($order['zip_code']);
?>
            </p>
            <p><strong>Country:</strong>
              <?php echo htmlspecialchars($order['country']);
?>
            </p>
            <p><strong>Payment:</strong>
              <?php echo htmlspecialchars($paymentMethod ==='cod' ? 'Cash on Delivery (COD)' : 'Online Payment');
?>
            </p>
          </div>
          <div class="order-summary">
            <h2>Your Order Summary</h2>
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
                <?php foreach ($items as $item): ?>
                <tr>
                  <td><img src="<?php echo htmlspecialchars($item['item_image']); ?>" alt="Product Image"
                      class="item-image"></td>
                  <td>
                    <?php echo htmlspecialchars($item['product_name']);
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
            <p class="total-price">Total Price: $
              <?php echo number_format($order['total_price'], 2);
?>
            </p>
          </div>
          <?php if ($order['status'] !=='Cancelled'): ?>
          <form action="order_details.php?id=<?php echo $orderId; ?>" method="POST"><input type="submit"
              name="cancel_order" class="btn btn-cancel" value="Cancel Order"></form>
          <?php endif;
?>
          <?php else: ?>
          <p>Order not found.</p>
          <?php endif;
?>
        </div>
        <div class="ship">
          <h1>Thank You !</h1><img src="/SMW/Phone Skin/Assets/pixelcut-export.png" alt="">
        </div>
        <!-- ----------------------------FOOTER---------------------------------- -->
          <footer class="footer">
            <div class="column">
              <h1>Vision</h1>
              <h2>Creativity,
                Expression,
                & Exploration</h2>
            </div>
            <div class="column">
              <h1>Pages</h1><a href="../SMW/Phone Skin/About/About.html">
                <h2>About Us</h2>
              </a>
              <h2>Contact Us</h2><a href="../SMW/Phone Skin/Condition/index.html">
                <h2>Terms and Conditions</h2>
              </a><a href="../SMW/Phone Skin/Policy/index.html">
                <h2>Privacy Policy</h2>
              </a>
              <h2>Refund/Cancellations,
                Shipping Policy</h2>
              <h2>FAQ</h2>
            </div>
            <div class="column">
              <h1>My Accounts</h1><a href="login.php">
                <h2>Login</h2>
              </a><a href="register.php">
                <h2>Register</h2>
              </a>
              <h2>Track Order</h2>
            </div>
            <div class="column">
              <h1>Follow Us</h1>
              <div class="social-icons"><a href="#"><img src="../SMW/Phone Skin/Assets/twit.png"
                    alt="Twitter">Twitter</a><a href="#"><img src="../SMW/Phone Skin/Assets/fbook.png"
                    alt="Facebook">Facebook</a><a href="#"><img src="../SMW/Phone Skin/Assets/ytube.png"
                    alt="YouTube">YouTube</a><a href="#"><img src="../SMW/Phone Skin/Assets/inst.png"
                    alt="Instagram">Instagram</a></div>
            </div>
          </footer>
          <div class="hr1">
            <hr>
          </div>
          <div class="Copyright">
            <p>&copy;
              2024 Your Company Name. All rights reserved.</p>
          </div>
          <script>const menuIcon = document.querySelector('.menu-icon');
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
                  }

                    , scrollDelay);
                }

                else if (xPos > sectionWidth * 0.9) {
                  // Scroll right
                  clearInterval(scrollInterval);

                  scrollInterval = setInterval(() => {
                    logos.scrollLeft += scrollAmount;
                    if (logos.scrollLeft >= (logos.scrollWidth - logos.clientWidth)) clearInterval(scrollInterval);
                  }

                    , scrollDelay);
                }

                else {
                  clearInterval(scrollInterval);
                }
              });

              logoSection.addEventListener('mouseleave', () => {
                clearInterval(scrollInterval);
              });
            });
          </script>
          <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
          <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
          <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  </body>

  </html>