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

function checkSessionValidity($pdo) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $stmt = $pdo->prepare("SELECT expires_at FROM sessions WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    
    $session = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($session && strtotime($session['expires_at']) > time()) {
        return true;
    } else {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }
}

// Call this function at the beginning of each protected page
checkSessionValidity($pdo);
?>
