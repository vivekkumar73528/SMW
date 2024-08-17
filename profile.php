<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="styles.css">
    </head>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.profile-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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

.profile-pic-label {
    cursor: pointer;
    display: inline-block;
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

.edit-btn, .submit-btn {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 10px;
    font-size: 16px;
}

.edit-btn:hover, .submit-btn:hover {
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
    <div class="profile-container">
        <h1>User Profile</h1>
        <div class="profile-header">
            <label for="file-input" class="profile-pic-label">
                <img id="profile-pic" src="default-pic.jpg" alt="Profile Picture" class="profile-pic">
            </label>
            <input type="file" id="file-input" accept="image/*" style="display: none;">
            <div class="profile-info">
                <h2 id="username">John Doe</h2>
                <p id="email">john.doe@example.com</p>
            </div>
        </div>
        <button id="edit-profile" class="edit-btn">Edit Profile</button>
        <div id="profile-form" class="profile-form">
            <h3>Edit Profile</h3>
            <form id="form">
                <label for="username-input">Username:</label>
                <input type="text" id="username-input" placeholder="Enter username">

                <label for="email-input">Email:</label>
                <input type="email" id="email-input" placeholder="Enter email">

                <button type="submit" class="submit-btn">Save Changes</button>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
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
            };
            reader.readAsDataURL(file);
            fileInput.value = ''; // Clear file input value
        }
    });
});

    </script>
</body>
</html>
