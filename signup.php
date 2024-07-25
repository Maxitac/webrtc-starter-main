<?php
$host = 'localhost';
$db = 'authentication';
$user = 'webrtcAdmin';
$pass = 'webRTCAdmin@123';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "Username already exists";
        exit();
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "Email already exists";
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the user into the database
    $stmt = $pdo->prepare("INSERT INTO Users (email, username, password_hash) VALUES (:email, :username, :password_hash)");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password_hash', $hashedPassword);

    if ($stmt->execute()) {
        // Get the ID of the newly created user
        $userId = $pdo->lastInsertId();

        // Assign the default role (participant) to the new user
        $roleId = 2; // Default role ID for participant
        $stmt = $pdo->prepare("INSERT INTO userrolesmapping (user_id, role_id) VALUES (:user_id, :role_id)");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':role_id', $roleId);
        $stmt->execute();

        echo "Sign-up successful! You can now <a href='login.php'>login</a>.";
    } else {
        echo "Sign-up failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .signup-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .signup-container h1 {
            margin-bottom: 20px;
        }
        .signup-container label {
            display: block;
            margin: 10px 0 5px;
            text-align: left;
        }
        .signup-container input[type="email"],
        .signup-container input[type="text"],
        .signup-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .signup-container input[type="submit"],
        .signup-container .login-button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .signup-container .login-button {
            background-color: #007bff;
            margin-top: 10px;
        }
        .signup-container input[type="submit"]:hover,
        .signup-container .login-button:hover {
            background-color: #218838;
        }
        .signup-container .login-button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h1>Sign Up</h1>
        <form method="POST" action="signup.php" id="signupForm">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required><br>
            <input type="submit" value="Sign Up">
            <div class="error" id="error-message"></div>
        </form>
        <button class="login-button" onclick="window.location.href='login.php'">Already have an account? Log In</button>
    </div>
    <script>
        document.getElementById('signupForm').addEventListener('submit', function(event) {
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirm_password').value;
            if (password !== confirmPassword) {
                event.preventDefault();
                document.getElementById('error-message').innerText = "Passwords do not match.";
            }
        });
    </script>
</body>
</html>
