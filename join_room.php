<?php
session_start();

// Check if room_id is set
if (!isset($_GET['room_id'])) {
    die("Room ID is required");
}

// Set the session variable for the room
$_SESSION['room_id'] = $_GET['room_id'];

// Redirect to the room page
header("Location: room.php");
exit();
?>
