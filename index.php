<?php
if (!isset($_GET['username'])) {
    header("Location : https://192.168.100.138:8181/login.php");
    exit();
}
if (!isset($_GET['roomid'])) {
    header("Location : https://192.168.100.138:8181/login.php");
    exit();
}
if (!isset($_GET['userid'])) {
    header("Location : https://192.168.100.138:8181/login.php");
    exit();
}

$username = $_GET['username'];
$roomid = $_GET['roomid'];
$user_id = $_GET['userid'];

require 'check_session.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>WebRTC Room</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta username="username" content="<?= htmlspecialchars($username)?>">
    <meta roomid="roomid" content="<?= htmlspecialchars($roomid)?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel='stylesheet' type='text/css' media='screen' href='styles.css'>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #007bff;
            color: white;
            padding: 10px;
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
            display: flex;
            flex-direction: row;
            width: 100%;
            margin-top: 20px;
        }
        .left-content {
            width: 75%;
        }
        .right-content {
            width: 25%;
            padding: 10px;
            background-color: #e9ecef;
            border-left: 1px solid #ccc;
        }
        #videos {
            margin-top: 20px;
        }
        #video-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: black;
            height: 400px;
            border-radius: 10px;
        }
        #video-wrapper video {
            max-width: 100%;
            max-height: 100%;
        }
        #waiting {
            display: none;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Welcome, <?= htmlspecialchars($username) ?></h1>
        <form method="GET" action="https://192.168.100.138:8181/logout.php">
            <input type="submit" value="Logout">
        </form>
    </div>
    <div class="container">
        <div class="left-content">
            <div class="row mb-3 mt-3 justify-content-md-center">
                <button id="call" class="btn btn-primary col-1">Call!</button>
                <button id="hangup" class="col-1 btn btn-primary">Hangup</button>
                <button id="mute" class="btn btn-secondary col-1">Mute</button>
                <button id="video" class="btn btn-secondary col-1">Video Off</button>
                <button id="screen-share" class="btn btn-secondary col-1">Share Screen</button>
                <div id="answer" class="col"></div>
            </div>
            <div id="videos">
                <div id="video-wrapper">
                    <div id="waiting" class="btn btn-warning">Waiting for answer...</div>
                    <video class="video-player" id="video-player" autoplay playsinline controls></video>
                </div>
            </div>
        </div>
        <div class="right-content">
            <!-- New tab for future functionality -->
        </div>
    </div>
</body>
<script src="/socket.io/socket.io.js"></script>
<script src='scripts.js'></script>
<script src='socketListeners.js'></script>
</html>
