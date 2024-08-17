<?php session_start();
include 'db.php';

$orderId =$_GET['order_id'] ?? null;

if ($orderId ===null) {
  echo "Order ID is missing.";
  exit();
}

// Fetch order details
$stmt =$pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order =$stmt->fetch(PDO::FETCH_ASSOC);

if ( !$order) {
  echo "Order not found.";
  exit();
}

// Fetch order items
$stmt =$pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$orderId]);
$orderItems =$stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="vivo.css">
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
        background-color: #fff;
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
        /* margin: 20px 0; */
        /* Adjust the space above and below the <hr> */
      }

      .hr hr {
        border: 0;
        /* Removes the default border */
        height: 1px;
        /* Sets the height to a small value */
        background: #ddd;
        color: #ddd;
        /* Light gray background color */
        width: 80%;
        /* Sets the width of the <hr> */
        margin: 0 auto;
        /* Centers the <hr> horizontally */
      }

      /* ----------------------
            CONTAINER
        -----------------------*/
      .container {

        max-width: 900px;
        margin: 40px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      }

      .container h1 {
        /* margin-left: 120px; */
        /* text-align: left; */
        text-align: center;
        position: relative;
        font-size: 2.5em;
        margin-top: 10px;
        color: light green;
        /* font-size: 60px; */
      }

      .container h1::after {
        content: '';
        display: block;
        width: 200px;
        height: 2px;
        background-color: #333;
        position: absolute;
        bottom: -5px;
        left: 0;
        margin-left: 330px;

      }

      .order-summary,
      .shipping-details {
        margin-bottom: 30px;
        margin-top: 10px;
      }

      .order-summary th {
        background-color: #52c1d1;
      }


      .order-summary h2,
      .shipping-details h2 {
        color: #333;
        margin-top: 15px;
      }

      .shipping-details h2 {
        margin-bottom: 20px;
      }

      .shipping-details p {
        margin-top: 10px;
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
        color: #0560b6;
      }

      .item-image {
        max-width: 100px;
        height: auto;
      }

      /* ---------------------
     FOOTER
  ----------------------*/

      .footer {
        font-family: sans-serif;
        /* font-weight: 400px; */
        background-color: rgba(0, 0, 0, 0.18);
        color: #141414;
        padding: 40px 20px;
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
        font-style: bold;
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

        .container h1::after {
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

        .container h1::after {
          margin-left: 160px;
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

        .container h1::after {
          margin-left: 100px;
        }

        .shipping-details h2 {
          margin-top: 20px;
        }

        .item {
          width: 200px;
          grid-template-columns: 1fr;
          /* 1 column for very small screens */
        }

      }

      /* --------------------
  
       CONTAINER
         IMAGE

-------------------------*/


      .o-img {
        width: 100%;
        max-width: 200px;
        margin: 20px auto;
        padding: 10px;
        /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); */
        border-radius: 10px;
        overflow: hidden;
        /* background: linear-gradient(135deg, #f0f0f0, #dcdcdc); */
        transition: transform 0.3s, box-shadow 0.3s;
      }

      .o-img img {
        width: 100%;
        display: block;
        border-radius: 10px;
        transition: transform 0.3s, filter 0.3s;
      }

      .o-img:hover {
        transform: scale(1.05);
        /* box-shadow: 0 0px 0px rgba(0, 0, 0, 0.2); */
      }

      .o-img:hover img {
        filter: brightness(1.1);
        transform: scale(1.05);
      }
    </style>
 

  <body>
    <nav class="navbar">
      <div class="logo1"><a href="/SMW/Phone Skin/Home.html"><img src="/SMW/Phone Skin/Assets/LOGO.png" alt="Logo"></a>
      </div>
      <div class="nav-Links">
        <ul class="nav-links">
          <li class="dropdown"><a href="../SMW/Phone Skin/Mobile Skins/Mobile skin.html">Mobile Skins</a>
            <ul class="dropdown-content">
              <li><a href="../SMW/Phone Skin/Mobile Skins/iPhone/iPhone.html">iPhone</a></li>
              <li><a href="../SMW/Phone Skin/Mobile Skins/Samsung/Samsung.html">Samsung</a></li>
              <li><a href="../SMW/Phone SkinMobile Skins/Oneplus/Oneplus.html">Oneplus</a></li>
              <li><a href="../SMW/Phone Skin/Mobile Skins/Tecno/Tecno.html">Tecno</a></li>
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
              <li><a href="../SMW/Phone SKin/Skins Collection/Cyberforce/Cyberforce.html">Cyberforce</a></li>
              <li><a href="../SMW/Phone Skin/Skins Collection/Wanderlust/Wanderlust.html">Wanderlust</a></li>
              <li><a href="../SMW/Phone Skin/Skins Collection/Retro Funk/Retro.html">Retro</a></li>
              <li><a href="../Smw/Phone Skin/Skins Collection/Design Archive/Archive.html">Archive</a></li>
            </ul>
          </li>
          <li class="dropdown"><a href="../SMW/Phone Skin/Apply/Applye.html">How To Apply</a></li>
          <li class="dropdown"><a href="/SMW/login.php">Login</a></li>
        </ul>
      </div>
      <div class="icons"><img src="../SMW/Phone Skin/Assets/person-outline.svg" alt="User Icon" class="icon"><a
          href="/SMW/cart.php"><img src="../SMW/Phone Skin/Assets/cart.svg" alt="Cart Icon" class="icon"></a>
        <div class="menu-icon"><span></span><span></span><span></span></div>
      </div>
    </nav>
    <div class="hr">
      <hr>
    </div>

    <!-- -------------------------ORDER-------------------------- -->

      <div class="container">
        <h1>Order Confirmation</h1>
        <div class="o-img"><img src="/SMW/Phone Skin/Assets/ORDER.jpg" alt=""></div>
        <div class="shipping-details">
          <h2>Shipping Information</h2>
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
          <p><strong>Payment Method:</strong>
            <?php echo htmlspecialchars($order['shipping_method']==='cod' ? 'Cash on Delivery (COD)' : 'Online Payment');
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
              <?php foreach ($orderItems as $item): ?>
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
          <p class="total-price">Total Price: ₹
            <?php echo number_format($order['total_price'], 2);
?>
          </p>
        </div>
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
        <hr>
        <div class="Copyright">
          <p>&copy;

            2024 Your Company Name. All rights reserved.</p>
        </div>
        <script>document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.remove-button').forEach(button => {
              button.addEventListener('click', () => {
                const itemId = button.getAttribute('data-id');

                if (confirm('Are you sure you want to remove this item?')) {
                  fetch('', {

                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/x-www-form-urlencoded'
                    }

                    ,
                    body: `item_id=$ {
                  itemId
                }

                `

                  }).then(response => response.text()).then(result => {
                    if (result === 'success') {
                      document.getElementById(`item-$ {
                      itemId
                    }

                    `).remove();
                    }

                    else {
                      alert('There was an error removing the item.');
                    }

                  }).catch(error => {
                    console.error('Error:', error);
                    alert('There was an error removing the item.');
                  });
                }
              });
            });
          });



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





          document.addEventListener('DOMContentLoaded', function () {
            const img = document.querySelector('.o-img img');

            img.addEventListener('load', function () {
              img.classList.add('loaded');
            });
          });


        </script>
  </body>

  </html>