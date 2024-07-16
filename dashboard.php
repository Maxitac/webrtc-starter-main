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

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username FROM Users WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $user['username'];

// Fetch rooms from the database
$stmt = $pdo->query("SELECT * FROM rooms");
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check user role
$stmt = $pdo->prepare("SELECT role_id FROM userrolesmapping WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$userRole = $stmt->fetch(PDO::FETCH_ASSOC);
$isHost = ($userRole && $userRole['role_id'] == 1);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome to the Dashboard, <?= htmlspecialchars($username) ?></h1>
    <h2>Available Rooms</h2>
    <ul id="rooms-list">
        <?php foreach ($rooms as $room): ?>
            <li data-room-id="<?= htmlspecialchars($room['id']) ?>">
                <?= htmlspecialchars($room['name']) ?>
                - <?= htmlspecialchars($room['description']) ?>
                <button class="join-room">Join</button>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($isHost): ?>
        <form method="POST" action="create_room.php">
            <h3>Create a New Room</h3>
            <label for="name">Room Name:</label>
            <input type="text" id="name" name="name" required><br>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea><br>
            <input type="submit" value="Create Room">
        </form>
    <?php endif; ?>

    <form method="POST" action="logout.php">
        <input type="submit" value="Logout">
    </form>

    <script>
        document.querySelectorAll('.join-room').forEach(button => {
            button.addEventListener('click', function() {
                const roomId = this.parentElement.getAttribute('data-room-id');
                window.location.href = `join_room.php?room_id=${roomId}`;
            });
        });
    </script>
</body>
</html>
