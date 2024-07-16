<?php
include 'check_session.php';

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

// Check if the user is a host
$stmt = $pdo->prepare("SELECT role_id FROM userrolesmapping WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$userRole = $stmt->fetch(PDO::FETCH_ASSOC);

if ($userRole && $userRole['role_id'] == 1) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $description = $_POST['description'];

        // Insert the new room into the database
        $stmt = $pdo->prepare("INSERT INTO rooms (name, description) VALUES (:name, :description)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Failed to create room. Please try again.";
        }
    }
} else {
    echo "You do not have permission to create a room.";
    exit();
}
?>
