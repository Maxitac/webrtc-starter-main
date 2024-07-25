<?php
session_start();

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
    $login = $_POST['login'];
    $password = $_POST['password'];

    // Query to fetch the user by username or email
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = :login OR email = :login");
    $stmt->bindParam(':login', $login);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];

        // Insert the session details into the sessions table
        $user_id = $user['user_id'];
        $created_at = date('Y-m-d H:i:s');
        $session_token = bin2hex(random_bytes(16)); // Generate a unique session token

        $insertSessionStmt = $pdo->prepare("INSERT INTO sessions (user_id, created_at, session_token) VALUES (:user_id, :created_at, :session_token)");
        $insertSessionStmt->bindParam(':user_id', $user_id);
        $insertSessionStmt->bindParam(':created_at', $created_at);
        $insertSessionStmt->bindParam(':session_token', $session_token);
        $insertSessionStmt->execute();

        header("Location: dashboard.php");
        exit();
    } else {
        echo "Invalid login credentials";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .login-container h1 {
            margin-bottom: 20px;
        }
        .login-container label {
            display: block;
            margin: 10px 0 5px;
            text-align: left;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-container input[type="submit"],
        .login-container .signup-button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-container .signup-button {
            background-color: #007bff;
            margin-top: 10px;
        }
        .login-container input[type="submit"]:hover,
        .login-container .signup-button:hover {
            background-color: #218838;
        }
        .login-container .signup-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <form method="POST" action="login.php">
            <label for="login">Username or Email:</label>
            <input type="text" id="login" name="login" required><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>
            <input type="submit" value="Login">
        </form>
        <button class="signup-button" onclick="window.location.href='signup.php'">Sign Up</button>
    </div>
</body>
</html>
