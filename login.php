<?php
session_start();

$host = 'localhost';
$db = 'authentication';
$user = 'cnsadmin';
$pass = 'cnsadmin@123';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usernameOrEmail = $_POST['username_or_email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = :username_or_email OR email = :username_or_email");
    $stmt->bindParam(':username_or_email', $usernameOrEmail);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        
        // Insert session record
        $stmt = $pdo->prepare("INSERT INTO sessions (user_id, created_at, expires_at) VALUES (:user_id, NOW(), DATE_ADD(NOW(), INTERVAL 3 HOUR))");
        $stmt->bindParam(':user_id', $user['id']);
        $stmt->execute();

        // Redirect to the desired URL after successful login
        $redirectUrl = 'https://192.168.100.138:8181';
        header("Location: $redirectUrl");
        exit();
    } else {
        echo "Invalid username/email or password";
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
        <label for="username_or_email">Username or Email:</label>
        <input type="text" id="username_or_email" name="username_or_email" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
