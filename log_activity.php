<?php
include 'check_session.php';

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

// Fetch user ID from session
$user_id = $_SESSION['user_id'];

// Get the posted activity type
$data = json_decode(file_get_contents('php://input'), true);
$activity_type = $data['activity_type'];

// Log the activity
$stmt = $pdo->prepare("INSERT INTO useractivity (user_id, activity_type) VALUES (:user_id, :activity_type)");
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':activity_type', $activity_type);
$stmt->execute();
?>
