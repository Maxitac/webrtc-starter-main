const userName = "Rob-" + Math.floor(Math.random() * 100000);
const password = "x";
document.querySelector('#user-name').innerHTML = userName;

// if trying it on a phone, use this instead...
const socket = io.connect('https://192.168.100.138:8181/', {
    // const socket = io.connect('https://localhost:8181/', {
    auth: {
        userName,
        password
    }
});

const localVideoEl = document.querySelector('#local-video');
const remoteVideoEl = document.querySelector('#remote-video');

let localStream;
let remoteStream;
let peerConnection;
let didIOffer = false;

let peerConfiguration = {
    iceServers: [
        {
            urls: [
                'stun:stun.l.google.com:19302',
                'stun:stun1.l.google.com:19302'
            ]
        }
    ]
};

const muteMicrophone = () => {
    const audioTrack = localStream.getAudioTracks()[0];
    if(audioTrack){
        audioTrack.enabled = !audioTrack.enabled;
        document.querySelector('#mute').innerText = audioTrack.enabled ? "Mute" : "Unmute";

    }
};

const toggleVideo = () => {
    const videoTrack = localStream.getVideoTracks()[0];
    if(videoTrack){
        videoTrack.enabled = !videoTrack.enabled;
        document.querySelector('#video').innerText = videoTrack.enabled ? "Video Off" : "Video On";
    }
};

document.querySelector('#mute').addEventListener('click', muteMicrophone);
document.querySelector('#video').addEventListener('click', toggleVideo);

// when a client initiates a call
const call = async e => {
    await fetchUserMedia();
    await createPeerConnection();

    try {
        console.log("Creating offer...");
        const offer = await peerConnection.createOffer();
        console.log(offer);
        peerConnection.setLocalDescription(offer);
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
        localVideoEl.srcObject = null;
        remoteVideoEl.srcObject = null;
        console.log("Call ended.");
        window.location.href = '/';
    }
};

const captureScreen = async () => {
    try{
        const screenStream = await navigator.mediaDevices.getDisplayMedia({video : true});
        return screenStream;
    } catch(err){
        console.error("Error: " + err);
        return null
    }
};

const switchToScreenshare = async() => {
    const screenStream = await captureScreen();
    if(screenStream){
        const screenTrack = screenStream.getVideoTracks()[0];
        const sender = peerConnection.getSenders().find(s => s.track.kind === 'video');
        sender.replaceTrack(screenTrack);
        localVideoEl.srcObject = screenStream;
        screenTrack.onended = () => {
            const webcamTrack = localStream.getVideoTracks()[0];
            sender.replaceTrack(webcamTrack);
            localVideoEl.srcObject = localStream;
        };
    }
};

/*const addStopScreenShareButton = (screenTrack, sender) => {
    const controlsDiv = document.querySelector('#controls');
    const stopScreenShareButton = document.createElement('button');
    stopScreenShareButton.id = 'stop-screen-share';
    stopScreenShareButton.className = 'btn btn-secondary col-1';
    stopScreenShareButton.innerText = 'Stop Screen Share';

    stopScreenShareButton.addEventListener('click', () => {
        screenTrack.stop();
        revertToWebcam(sender);
    });

    controlsDiv.appendChild(stopScreenShareButton);
};

const revertToWebcam = (sender) => {
    const webcamTrack = localStream.getVideoTracks()[0];
    sender.replaceTrack(webcamTrack);
    localVideoEl.srcObject = localStream;

    // Remove the stop screen share button
    const stopScreenShareButton = document.querySelector('#stop-screen-share');
    if (stopScreenShareButton) {
        stopScreenShareButton.remove();
    }
}; */

const answerOffer = async (offerObj) => {
    await fetchUserMedia();
    await createPeerConnection(offerObj);
    const answer = await peerConnection.createAnswer({});
    await peerConnection.setLocalDescription(answer);
    console.log(offerObj);
    console.log(answer);
    offerObj.answer = answer;
    const offerIceCandidates = await socket.emitWithAck('newAnswer', offerObj);
    offerIceCandidates.forEach(c => {
        peerConnection.addIceCandidate(c);
        console.log("======Added Ice Candidate======");
    });
    console.log(offerIceCandidates);
};

const addAnswer = async (offerObj) => {
    await peerConnection.setRemoteDescription(offerObj.answer);
};

const fetchUserMedia = () => {
    return new Promise(async (resolve, reject) => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: true,
                audio: true,
            });
            localVideoEl.srcObject = stream;
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
        peerConnection = await new RTCPeerConnection(peerConfiguration);
        remoteStream = new MediaStream();
        remoteVideoEl.srcObject = remoteStream;

        localStream.getTracks().forEach(track => {
            peerConnection.addTrack(track, localStream);
        });

        peerConnection.addEventListener("signalingstatechange", (event) => {
            console.log(event);
            console.log(peerConnection.signalingState);
        });

        peerConnection.addEventListener('icecandidate', e => {
            console.log('........Ice candidate found!......');
            console.log(e);
            if (e.candidate) {
                socket.emit('sendIceCandidateToSignalingServer', {
                    iceCandidate: e.candidate,
                    iceUserName: userName,
                    didIOffer,
                });
            }
        });

        peerConnection.addEventListener('track', e => {
            console.log("Got a track from the other peer!! How exciting");
            console.log(e);
            e.streams[0].getTracks().forEach(track => {
                remoteStream.addTrack(track, remoteStream);
                console.log("Here's an exciting moment... fingers crossed");
            });
        });

        if (offerObj) {
            await peerConnection.setRemoteDescription(offerObj.offer);
        }
        resolve();
    });
};

const addNewIceCandidate = iceCandidate => {
    peerConnection.addIceCandidate(iceCandidate);
    console.log("======Added Ice Candidate======");
};

document.querySelector('#call').addEventListener('click', call);
document.querySelector('#hangup').addEventListener('click', hangup);
document.querySelector('#screen-share').addEventListener('click', switchToScreenshare);
