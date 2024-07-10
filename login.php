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

        header("Location: https://10.51.60.244:8181");
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
</head>
<body>
    <form method="POST" action="login.php">
        <label for="login">Username or Email:</label>
        <input type="text" id="login" name="login" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
