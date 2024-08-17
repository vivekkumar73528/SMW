<?php
session_start();

// Hard-coded admin credentials
$hardcoded_username = 'admin';
$hardcoded_password = 'password';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate admin credentials
    if ($username === $hardcoded_username && $password === $hardcoded_password) {
        // Set session variable
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $loginError = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
/* General container styling */
.container {
    display: flex;
    width: 100%;
    max-width: 1200px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 100vh;
    background-color: #f4f4f4;
    font-family: Arial, sans-serif;
    padding: 0 20px;
}

/* Admin panel section */
.AD {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    animation: fadeIn 1s ease-out;
    width: 100%; /* Adjust width as needed */
    margin-left: 18%;
}

.AD h1 {
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 2rem;
    color: #333;
    margin-top: 40%;
    /* margin-bottom: 20px;  */
}

.AD img {
    width: 550px;
    height: auto;
    border-radius: 8px;
    margin: 0; /* Remove margin for proper centering */
    transition: transform 0.3s ease;
    margin-right: 30%;
}

.AD img:hover {
    transform: scale(1.05);
}

/* Login container styling */
.login-container {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    animation: slideUp 1s ease-out;
    width: 100%; /* Adjust the width as needed */
    margin-right: 50px;
}

.login-container h2 {
    font-size: 2rem;
    color: #555;
    margin-bottom: 15px;
    text-align: center;
}

/* Form styling */
form {
    display: flex;
    flex-direction: column;
}

.form-group {
    margin-bottom: 15px;
}

label {
    font-size: 1rem;
    color: #666;
    display: block;
    margin-bottom: 5px;
}

input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}

input[type="text"]:focus,
input[type="password"]:focus {
    border-color: #007bff;
    outline: none;
}

/* Error message styling */
.error-message {
    color: #e74c3c;
    font-size: 0.875rem;
    margin-bottom: 15px;
    text-align: center;
}

/* Submit button styling */
.submit-button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 15px;
    font-size: 1rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.submit-button:hover {
    background-color: #0056b3;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}





    </style>
</head>
<body>
    <div class="container">
    <div class="AD">
        <!-- <h1>Welcome To Admin Panel</h1> -->
        <img src="/SMW/Admin/Images/AD.png" alt="Description">
    </div>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form action="admin_login.php" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <?php if (isset($loginError)): ?>
                <div class="error-message"><?= htmlspecialchars($loginError) ?></div>
            <?php endif; ?>
            <button type="submit" class="submit-button">Login</button>
        </form>
    </div>
    </div>
</body>
</html>
