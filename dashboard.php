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

// Handle form submission for elevating participant to host
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['elevate_username'])) {
    $elevateUsername = $_POST['elevate_username'];

    // Find user ID based on the provided username/email
    $stmt = $pdo->prepare("SELECT user_id FROM Users WHERE username = :username OR email = :username");
    $stmt->bindParam(':username', $elevateUsername);
    $stmt->execute();
    $elevateUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($elevateUser) {
        $elevateUserId = $elevateUser['user_id'];

        // Update user's role to host (role_id = 1)
        $updateStmt = $pdo->prepare("UPDATE userrolesmapping SET role_id = 1 WHERE user_id = :user_id");
        $updateStmt->bindParam(':user_id', $elevateUserId);
        $updateStmt->execute();

        echo "<p>User $elevateUsername has been elevated to host.</p>";
    } else {
        echo "<p>User $elevateUsername not found.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .navbar {
            background-color: #007bff;
            color: white;
            padding: 10px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .navbar h1 {
            margin: 0;
        }
        .navbar form {
            margin: 0;
        }
        .navbar input[type="submit"] {
            background-color: red;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .container {
            width: 90%;
            max-width: 800px;
            background-color: white;
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #007bff;
        }
        #rooms-list {
            list-style: none;
            padding: 0;
        }
        #rooms-list li {
            background-color: #e9ecef;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        #rooms-list li button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        #rooms-list li button:hover {
            background-color: #218838;
        }
        .host-actions {
            margin-top: 20px;
        }
        .host-actions h3 {
            cursor: pointer;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin: 0;
            margin-bottom: 10px;
        }
        .host-actions form {
            display: none;
            margin: 0;
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 5px;
        }
        .host-actions form label {
            display: block;
            margin: 10px 0 5px;
        }
        .host-actions form input[type="text"],
        .host-actions form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .host-actions form input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Welcome, <?= htmlspecialchars($username) ?></h1>
        <form method="POST" action="logout.php">
            <input type="submit" value="Logout">
        </form>
    </div>
    <div class="container">
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
            <div class="host-actions">
                <h3 onclick="toggleForm('create-room-form')">Create a New Room</h3>
                <form method="POST" action="create_room.php" id="create-room-form">
                    <label for="name">Room Name:</label>
                    <input type="text" id="name" name="name" required><br>
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea><br>
                    <input type="submit" value="Create Room">
                </form>

                <h3 onclick="toggleForm('elevate-form')">Elevate Participant to Host</h3>
                <form method="POST" action="" id="elevate-form">
                    <label for="elevate_username">Username/Email:</label>
                    <input type="text" id="elevate_username" name="elevate_username" required><br>
                    <input type="submit" value="Elevate to Host">
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function toggleForm(formId) {
            const form = document.getElementById(formId);
            form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
        }

        document.querySelectorAll('.join-room').forEach(button => {
            button.addEventListener('click', function() {
                const roomId = this.parentElement.getAttribute('data-room-id');
                window.location.href = `join_room.php?room_id=${roomId}`;
            });
        });
    </script>
</body>
</html>
