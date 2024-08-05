
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>WebRTC Room</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    
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
            flex-direction: column;
            width: 100%;
            margin-top: 20px;
        }
        .left-content {
            width: 100%;
        }
        #videos {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        #video-wrapper {
            background-color: black;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            width: 49%;
            height: 400px;
        }
        #video-wrapper video {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
        }
        #waiting {
            display: none;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Welcome, <span id="user-name"></span></h1>
        <form method="GET" action="https://192.168.100.138:8181/login.php">
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
                    <video class="video-player" id="local-video" autoplay playsinline controls></video>
                </div>
                <div id="video-wrapper">
                    <video class="video-player" id="remote-video" autoplay playsinline controls></video>
                </div>
            </div>
        </div>
    </div>
    <script src="/socket.io/socket.io.js"></script>
    <script src='scripts.js'></script>
    <script src='socketListeners.js'></script>
</body>
</html>