/*const {createPool} = require('mysql');

const pool = createPool({
    host:"localhost",
    user:"webrtcAdmin",
    password:"webRTCAdmin@123",
    database:"authentication",
    connectionLimit: 10

})*/

/*const userName = "<?php echo htmlspecialchars($_SESSION['user_name']); ?>";
console.log(userName);
const password = "x";
const roomId = "<?php echo htmlspecialchars($_SESSION['room_id']); ?>";
console.log(roomId);
document.querySelector('#user-name').innerHTML = userName;*/

const userName = document.querySelector('meta[username="username"]').content;
console.log('Hello ' + userName);
const roomId = document.querySelector('meta[roomid="roomid"]').content;
console.log('You are in room ' + roomId);
const password = "x";

const socket = io.connect('https://192.168.100.138:8181', {
    auth: { userName, password, roomId }
});

const videoPlayerEl = document.querySelector('#video-player');

let localStream;
let remoteStream;
let peerConnection;
let didIOffer = false;
let isStreaming = false;

const peerConfiguration = {
    iceServers: [
        { urls: ['stun:stun.l.google.com:19302', 'stun:stun1.l.google.com:19302'] }
    ]
};

const muteMicrophone = () => {
    const audioTrack = localStream.getAudioTracks()[0];
    if (audioTrack) {
        audioTrack.enabled = !audioTrack.enabled;
        document.querySelector('#mute').innerText = audioTrack.enabled ? "Mute" : "Unmute";
    }
};

const toggleVideo = () => {
    const videoTrack = localStream.getVideoTracks()[0];
    if (videoTrack) {
        videoTrack.enabled = !videoTrack.enabled;
        document.querySelector('#video').innerText = videoTrack.enabled ? "Video Off" : "Video On";
    }
};

document.querySelector('#mute').addEventListener('click', muteMicrophone);
document.querySelector('#video').addEventListener('click', toggleVideo);

const call = async e => {
    await fetchUserMedia();
    await createPeerConnection();
    try {
        const offer = await peerConnection.createOffer();
        await peerConnection.setLocalDescription(offer);
        didIOffer = true;
        socket.emit('newOffer', offer);
    } catch (err) {
        console.log(err);
    }
};

const hangup = async e => {
    if (peerConnection) {
        peerConnection.close();
        peerConnection = null;
        socket.emit('hangup');
        videoPlayerEl.srcObject = null;
        console.log("User " + userName + " has hung up");
        window.location.href = 'https://192.168.100.138:8181/dashboard.php';
    }
};

const captureScreen = async () => {
    try {
        const screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
        return screenStream;
    } catch (err) {
        console.error("Error: " + err);
        return null;
    }
};

const switchToScreenshare = async () => {
    const screenStream = await captureScreen();
    if (screenStream) {
        const screenTrack = screenStream.getVideoTracks()[0];
        const sender = peerConnection.getSenders().find(s => s.track.kind === 'video');
        sender.replaceTrack(screenTrack);
        videoPlayerEl.srcObject = screenStream;
        screenTrack.onended = () => {
            const webcamTrack = localStream.getVideoTracks()[0];
            sender.replaceTrack(webcamTrack);
            videoPlayerEl.srcObject = localStream;
        };
    }
};

const answerOffer = async (offerObj) => {
    await fetchRemoteMedia();
    await createPeerConnection(offerObj);
    const answer = await peerConnection.createAnswer({});
    await peerConnection.setLocalDescription(answer);
    offerObj.answer = answer;
    const offerIceCandidates = await socket.emitWithAck('newAnswer', offerObj);
    offerIceCandidates.forEach(c => peerConnection.addIceCandidate(c));
};

const addAnswer = async (offerObj) => {
    await peerConnection.setRemoteDescription(offerObj.answer);
};

const fetchUserMedia = async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        
        localStream = stream;
        videoPlayerEl.srcObject = stream;
        videoPlayerEl.muted = true; // Ensure the local video feed is muted to avoid feedback
        videoPlayerEl.play(); // Explicitly play the video to ensure it's visible
    } catch (err) {
        console.log(err);
    }
    const videoTrack = localStream.getVideoTracks()[0];
        if (videoTrack){
            videoTrack.enabled = false; // Video is off by default, mic still active.
        }
};

const fetchRemoteMedia = () => {
    return new Promise(async (resolve, reject) => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: false, audio: true });
            const audioTrack = stream.getAudioTracks()[0];
            if (audioTrack) {
                audioTrack.enabled = false; // Mute the microphone by default
            }
            localStream = stream;
            resolve();
        } catch (err) {
            console.log(err);
            reject();
        }
    });
};

const createPeerConnection = (offerObj) => {
    return new Promise(async (resolve, reject) => {
        peerConnection = new RTCPeerConnection(peerConfiguration);
        remoteStream = new MediaStream();
        videoPlayerEl.srcObject = remoteStream;

        localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

        peerConnection.addEventListener("signalingstatechange", event => console.log(event, peerConnection.signalingState));
        peerConnection.addEventListener('icecandidate', e => {
            if (e.candidate) {
                socket.emit('sendIceCandidateToSignalingServer', {
                    iceCandidate: e.candidate,
                    iceUserName: userName,
                    didIOffer,
                });
            }
        });

        peerConnection.addEventListener('track', e => {
            e.streams[0].getTracks().forEach(track => remoteStream.addTrack(track, remoteStream));
        });

        if (offerObj) {
            await peerConnection.setRemoteDescription(offerObj.offer);
        }
        resolve();
    });
};

const addNewIceCandidate = iceCandidate => {
    peerConnection.addIceCandidate(iceCandidate);
};

document.querySelector('#call').addEventListener('click', call);
document.querySelector('#hangup').addEventListener('click', hangup);
document.querySelector('#screen-share').addEventListener('click', switchToScreenshare);

// New events to handle start and stop streaming
document.querySelector('#start-stream').addEventListener('click', () => {
    if (!isStreaming) {
        socket.emit('start-stream');
        isStreaming = true;
    }
});

document.querySelector('#stop-stream').addEventListener('click', () => {
    if (isStreaming) {
        socket.emit('stop-stream');
        isStreaming = false;
    }
});

socket.on('availableOffers', offers => offers.forEach(answerOffer));
socket.on('newOfferAwaiting', offer => answerOffer(offer));
socket.on('answerResponse', offerObj => addAnswer(offerObj));
socket.on('receivedIceCandidateFromServer', iceCandidate => addNewIceCandidate(iceCandidate));

// Handle new streamer and streamer disconnection events
socket.on('new-streamer', streamerId => {
    // Request the new streamer's video stream
    if (peerConnection) {
        peerConnection.close();
        peerConnection = null;
    }
    call();  // Re-initiate the call
});

socket.on('streamer-disconnected', () => {
    // Handle the streamer's disconnection (e.g., stop the video)
    if (peerConnection) {
        peerConnection.close();
        peerConnection = null;
        console.log("The following user has disconnected: " + userName);
    }
    videoPlayerEl.srcObject = null;
});


