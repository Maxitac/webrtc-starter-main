# WebRTC Video Chat Project

## Introduction
This project is a WebRTC-based application designed to facilitate video conferencing. Users can join rooms, share their video and audio streams, and utilize screen sharing functionalities.

## Features
- User authentication and authorization
- Room-based video conferencing
- Mute/Unmute microphone
- Toggle video on/off
- Screen sharing
- Role-based controls (Host and Participant)

## Functional Requirements
- Users must be able to sign up and log in.
- Users must be placed in a room upon logging in.
- Hosts should have control over the video stream being shared.
- Participants should be able to share their video, audio, and screen when granted permission by the host.

## Non-Functional Requirements
- The application should ensure data security and privacy.
- The system should handle concurrent users efficiently.
- The application should provide a user-friendly interface.

## Installation

### Prerequisites
- Node.js
- npm
- MySQL

### Steps
1. Clone the repository:
    ```sh
    git clone https://github.com/Maxitac/webrtc-starter.git
    cd webrtc-starter
    ```

2. Install dependencies:
    ```sh
    npm install
    ```

3. Set up the MySQL database:
    - Create a database named `authentication`.
    - Run the provided SQL script to create tables and set up initial data.

4. Configure the database connection in `signup.php` and `login.php`:
    ```php
    $host = 'localhost';
    $db = 'authentication';
    $user = 'your_db_user';
    $pass = 'your_db_password';
    ```

5. Start the server:
    ```sh
    nodemon ./server.js
    ```

6. Open your browser and navigate to `http://localhost:3000` or via your IP Address as defined in scripts.js and server.js.

## Usage
### Sign Up
1. Go to the Sign-Up page.
2. Enter your email, username, and password.
3. Click "Sign Up".

### Log In
1. Go to the Log-In page.
2. Enter your username or email and password.
3. Click "Login".

### Video Conferencing
1. Upon logging in, you will be placed in a room.
2. Use the provided buttons to mute/unmute your microphone, toggle video, or share your screen.
3. Hosts can control which participant’s video is shared.

## Contributing
1. Fork the repository.
2. Create a new branch (`git checkout -b feature-branch`).
3. Make your changes.
4. Commit your changes (`git commit -m 'Add some feature'`).
5. Push to the branch (`git push origin feature-branch`).
6. Create a new Pull Request.

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
