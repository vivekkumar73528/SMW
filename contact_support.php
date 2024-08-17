<!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Support</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
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

      .footer .column {
        justify-content: flex-start;
        text-align: left;
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

        .footer .column {
          justify-content: flex-start;
          text-align: left;
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

        .container.main {
          padding: 10px;
          /* Adjust container padding for smaller screens */
        }

        /* Stack and Center Buttons */
        .sp {
          display: flex;
          flex-direction: column;
          align-items: center;
        }

        .sp a {
          width: 90%;
          /* Decrease button width */
          max-width: 300px;
          /* Set a max-width for larger screens */
          height: 50px;
          /* Set a fixed height for buttons */
          text-align: center;
          margin-bottom: 15px;
          /* Space between buttons */
          border-radius: 8px;
          /* Rounded corners for buttons */
          display: flex;
          align-items: center;
          justify-content: center;
          /* Center the text vertically */
          font-size: 16px;
          /* Font size for better readability */
        }

        /* Remove Padding for Specific Buttons */
        .sp a.btn-primary,
        .sp a.btn-outline-info,
        .sp a.btn-whatsapp {
          padding: 0;
          /* Remove padding */
          margin: 10px;
        }

        /* Adjust Font Sizes */
        .container.main h1 {
          font-size: 24px;
          /* Smaller header font size */
        }

        .container.main p {
          font-size: 16px;
          /* Adjust paragraph font size */
        }

        .alert.alert-info {
          font-size: 14px;
          /* Smaller font size in alerts */
          padding: 15px;
          /* Padding for alert box */
          border-radius: 10px;
          /* Rounded corners for alerts */
          margin-top: 20px;
          /* Margin on top of the alert box */
        }

        .alert.alert-info h4 {
          font-size: 16px;
          /* Smaller heading font size in alert */
          margin-bottom: 10px;
          /* Space below heading */
        }

        /* Stack Alert Items */
        .alert.alert-info p {
          margin-bottom: 10px;
          /* Space between items in alert */
        }

        /* Center and Space Back to Dashboard Button */
        .d {
          text-align: center;
          margin-top: 20px;
        }

        .d a {
          width: 100%;
          margin-top: 10px;
          /* Space between buttons */
          padding: 12px;
          /* Increased button padding */
          border-radius: 8px;
          /* Rounded corners for buttons */
        }

        /* Adjust Button Sizes */
        .btn {
          font-size: 14px;
          /* Smaller font size for buttons */
          padding: 12px;
          /* Adjust padding for buttons */
          border-radius: 8px;
          /* Rounded corners for buttons */
        }

        /* Icon Spacing */
        .icon {
          margin-right: 8px;
          /* Increase space between icon and text */
        }

        /* Responsive Images */
        img {
          max-width: 100%;
          height: auto;
          /* Ensure images scale correctly */
        }

        /* Hide Non-Essential Elements */
        .non-essential {
          display: none;
          /* Hide non-essential elements if needed */
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


      body {
        font-family: 'Roboto', sans-serif;
        background-color: #f0f2f5;
        color: #343a40;
        text-align: center;
        /* padding: 5px 0; */
      }

      .container {
        max-width: 900px;
        /* max-height: 800px; */
        margin: auto;
        padding: 50px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        margin-top: 50px;
      }

      .sp {
        margin-top: 40px;
      }

      .alert-info p {
        margin-top: 40px;
      }

      .alert {
        padding: 20px;
        border-radius: 5px;
        position: relative;
        margin-top: 50px;
      }

      .alert h4 {
        margin-top: 5px;
        color: #31708f;
        font-size: 1rem;
        /* font-weight: bold; */
      }

      .btn-outline-info {
        color: #31708f;
        padding: 5px 2px;
        border-radius: 5px;
        text-decoration: none;
        transition: all 0.3s ease;
        /* margin-top: 20px; */
      }

      .btn-outline-info:hover {
        background-color: #31708f;
        color: #fff;
        text-decoration: none;
      }


      .icon {
        margin-right: 5px;
      }

      .mt-4 {
        margin-top: 1.5rem;
      }

      .d {
        margin-top: 20px;
      }

      .btn {
        gap: 10px;
        border-radius: 30px;
        padding: 14px 24px;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.3s, color 0.3s;
        margin-top: 10%;
      }

      .btn-primary {
        background-color: #007bff;
        color: #ffffff;
        border: none;

      }

      .btn-primary:hover {
        background-color: #0056b3;
      }

      .btn-outline-info {
        border: 2px solid #17a2b8;
        color: #17a2b8;
        margin-top: 50%;
      }

      .btn-outline-info:hover {
        background-color: #17a2b8;
        color: #ffffff;
      }

      .btn-whatsapp {
        background-color: #25D366;
        color: #ffffff;
      }

      .btn-whatsapp:hover {
        background-color: #1DA851;
      }

      .icon {
        font-size: 24px;
        margin-right: 8px;
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
            <li class="dropdown"><a href="">Skin Collections</a>
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
        <div class="icons"><img src="../SMW/Phone Skin/Assets/person-outline.svg" alt="User Icon" class="icon"><a
            href="/SMW/cart.php"><img src="../SMW/Phone Skin/Assets/cart.svg" alt="Cart Icon" class="icon"></a>
          <div class="menu-icon"><span></span><span></span><span></span></div>
        </div>
      </nav>
      <div class="hr">
        <hr>
      </div>
      <!-- -------------------------MIDDLE---------------- -->
        <div class="container main">
          <h1 class="mb-4">Contact Support</h1>
          <p class="mb-4">If you have any issues or need assistance,
            please contact our support team using one of the methods below:</p>
          <div class="sp"><a href="mailto:infotechsolutionhub02@gmail.com" class="btn btn-primary"><i
                class="fas fa-envelope icon"></i>Email Support</a><a href="tel:+917488128089"
              class="btn btn-outline-info"><i class="fas fa-phone-alt icon"></i>Call Us</a><a
              href="https://wa.me/+917488128089" target="_blank" class="btn btn-whatsapp"><i
                class="fab fa-whatsapp icon"></i>Chat on WhatsApp</a></div>
          <!-- Additional Functionalities -->
            <div class="alert alert-info mt-4">
              <h4><i class="fas fa-info-circle"></i>Features</h4>
              <p><a href="feedback_form.php" class="btn btn-outline-info"><i class="fas fa-comment-dots icon"></i>Submit
                  Feedback</a></p>
              <p><a href="./order_details.php" class="btn btn-outline-info" style=""><i
                    class="fas fa-box-open icon"></i>View Order History</a></p>
            </div>
            <div class="d"><a href="user_dashboard.php" class="btn btn-outline-info" id="desh"><i
                  class="fas fa-tachometer-alt icon"></i>Back to Dashboard</a></div>
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
          </div>>
          <!-- Bootstrap JS and dependencies (Optional) -->
            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
            <script> // -----------------MENU ICON -------------------------

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
            </script>
  </body>

  </html>