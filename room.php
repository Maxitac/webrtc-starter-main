<?php
include 'check_session.php';

// Check if the user has a valid session and room ID
if (!isset($_SESSION['user_id']) || !isset($_SESSION['room_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$room_id = $_SESSION['room_id'];

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

// Fetch room details
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = :room_id");
$stmt->bindParam(':room_id', $room_id);
$stmt->execute();
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    die("Room not found");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Room: <?= htmlspecialchars($room['name']) ?></title>
    <script src="scripts.js"></script>
    <script src="socketListeners.js"></script>
</head>
<body>
    <h1>Room: <?= htmlspecialchars($room['name']) ?></h1>
    <p><?= htmlspecialchars($room['description']) ?></p>
    <div>
        <video id="local-video" autoplay></video>
        <video id="remote-video" autoplay></video>
    </div>
    <button id="call">Call</button>
    <button id="hangup">Hang Up</button>
    <button id="mute">Mute</button>
    <button id="video">Video Off</button>
    <button id="screen-share">Share Screen</button>
</body>
</html>
