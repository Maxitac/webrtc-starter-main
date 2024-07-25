<?php
include 'check_session.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>WebRTC Room</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel='stylesheet' type='text/css' media='screen' href='styles.css'>
</head>
<body>
    <div class="container">
        <div class="row mb-3 mt-3 justify-content-md-center">
            <div id="user-name"><?php htmlspecialchars($_SESSION['user_name']); ?></div>
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
                <video class="video-player" id="local-video" autoplay playsinline controls></video>
            </div>
            <video class="video-player" id="remote-video" autoplay playsinline controls></video>
        </div>
    </div>
</body>
<script src="/socket.io/socket.io.js"></script>
<script src='scripts.js'></script>
<script src='socketListeners.js'></script>
</html>