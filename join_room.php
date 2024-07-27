<?php
session_start();

// Connect to the database
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

if (!isset($_SESSION['user_id'])) {
    // Redirect to login if user is not authenticated
    header("Location: login.php");
    exit();
}

if (!isset($_GET['room_id'])) {
    // Redirect to dashboard if no room_id is provided
    header("Location: dashboard.php");
    exit();
}

$room_id = $_GET['room_id'];
$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $pdo->prepare("SELECT username FROM Users WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_name = $user['username'];

// Delete any existing session data for this user
$deleteStmt = $pdo->prepare("DELETE FROM sessiondata WHERE username = :username");
$deleteStmt->bindParam(':username', $user_name);
$deleteStmt->execute();

// Insert new session data
$insertStmt = $pdo->prepare("INSERT INTO sessiondata (user_id, username, room_id) VALUES (:user_id, :username, :room_id)");
$insertStmt->bindParam(':user_id', $user_id);
$insertStmt->bindParam(':username', $user_name);
$insertStmt->bindParam(':room_id', $room_id);
$insertStmt->execute();

// Store room and user info in session
$_SESSION['room_id'] = $room_id;
$_SESSION['user_name'] = $user_name;

// Redirect to Node.js server URL
$url = "https://192.168.100.138:8181/index.php?username=" . urlencode($user_name) . "&roomid=" . urlencode($room_id) . "&userid=" . urlencode($user_id);
header("Location: $url");
exit();
?>
