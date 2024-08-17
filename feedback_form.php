<?php session_start();
include 'db.php';

function generateUniqueFeedbackId($pdo) {
  while (true) {
    $id =sprintf('%04d', mt_rand(0, 9999)); // Generate a 4-digit ID
    $stmt =$pdo->prepare("SELECT COUNT(*) FROM feedback WHERE feedback_id = ?");
    $stmt->execute([$id]);

    if ($stmt->fetchColumn()==0) {
      return $id;
    }
  }
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $feedback =$_POST['feedback'] ?? '';
  $userId =$_SESSION['user_id'] ?? null;

  if ($feedback && $userId) {
    try {
      $feedbackId =generateUniqueFeedbackId($pdo); // Generate a unique 4-digit ID

      $stmt =$pdo->prepare("INSERT INTO feedback (feedback_id, user_id, feedback_text, created_at) VALUES (?, ?, ?, NOW())");
      $stmt->execute([$feedbackId, $userId, $feedback]);
      $message ="Thank you for your feedback!";

    }

    catch (Exception $e) {
      $message ="An error occurred: " . $e->getMessage();
    }
  }

  else {
    $message ="Please provide feedback.";
  }
}

?>
<!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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




      /* ---------------------
       FOOTER
  ----------------------*/


      .footer {
        background-color: rgba(0, 0, 0, 0.18);
        color: #000;
        padding: 40px 20px;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
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

      .container {
        max-width: 600px;
        margin: auto;
        padding: 30px;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        text-align: center;
        margin-top: 20px;
      }

      .btn {
        border-radius: 30px;
        padding: 12px 24px;
        margin: 10px;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.3s, color 0.3s;
        align-items: center;
        justify-content: center;
      }

      .btn-primary {
        background-color: #007bff;
        color: #ffffff;
        border: none;
      }

      .btn-primary:hover {
        background-color: #0056b3;
      }

      .form-group {
        margin-bottom: 20px;
      }

      #r {
        width: 400%;
      }
    </style>

  <body>

  <!-- ---------------------------HEADER--------------------- -->

    <nav class="navbar">
      <div class="logo1"><a href="/SMW/Phone Skin/Home.html"><img src="/SMW/Phone Skin/Assets/LOGO.png" alt="Logo"></a>
      </div>
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
          </li>
          <li class="dropdown"><a href="../SMW/Phone Skin/Apply/Applye.html">How To Apply</a></li>
          <li class="dropdown"><a href="../SMW/login.php">Login</a></li>
        </ul>
      </div>
      <div class="icons"><img src="../SMW/Phone Skin/Assets/person-outline.svg" alt="User Icon" class="icon"><a
          href="/SMW/cart.php"><img src="../SMW/Phone Skin/Assets/cart.svg" alt="Cart Icon" class="icon"></a>
        <div class="menu-icon"><span></span><span></span><span></span></div>
      </div>
    </nav>
    <!-- ------------------------MIDDLE-------------------- -->
      <div class="hr">
        <hr>
      </div>
      <div class="container">
        <h1>Feedback Form</h1>
        <p>We value your feedback. Please let us know about your return experience.</p>
        <?php if (isset($message)): ?>
        <div class="alert alert-info">
          <?php echo $message;
?>
        </div>
        <?php endif;
?>
        <form method="POST" action="feedback_form.php">
          <div class="form-group"><label for="feedback">Your Feedback</label><textarea id="feedback" name="feedback"
              class="form-control" rows="5" placeholder="Enter your feedback here..." required></textarea></div><button
            type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i>Submit Feedback</button>
        </form><a href="user_dashboard.php" class="btn btn-outline-info"><i class="fas fa-tachometer-alt"></i>Back to
          Dashboard</a>
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
          <hr id="r">
        </footer>
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
  </body>

  </html>