const fs = require('fs');
const https = require('https');
const express = require('express');
const { createProxyMiddleware } = require('http-proxy-middleware');
const socketio = require('socket.io');
const { v4: uuidV4 } = require('uuid');

const app = express();
const key = fs.readFileSync('cert.key');
const cert = fs.readFileSync('cert.crt');
const expressServer = https.createServer({ key, cert }, app);
const io = socketio(expressServer, {
    cors: {
        origin: ["https://localhost","https://192.168.100.138"],
        methods: ["GET", "POST"]
    }
});
expressServer.listen(8181);

// Proxy requests to PHP server
const phpServer = 'http://localhost:80'; // Apache server URL

app.use('/', createProxyMiddleware({
    target: phpServer,
    changeOrigin: true,
    ws: true
}));

// WebRTC signaling code remains the same...
const rooms = {};
const streamers = {};
const offers = {};
const connectedSockets = {};

io.on('connection', (socket) => {
    const { userName, password, roomId } = socket.handshake.auth;

    if (password !== "x") {
        socket.disconnect(true);
        return;
    }

    if (!connectedSockets[roomId]) {
        connectedSockets[roomId] = [];
    }
    connectedSockets[roomId].push({ socketId: socket.id, userName });

    if (!offers[roomId]) {
        offers[roomId] = [];
    }

    socket.join(roomId);

    if (offers[roomId].length) {
        socket.emit('availableOffers', offers[roomId]);
    }

    if (!rooms[roomId]) {
        rooms[roomId] = [];
    }
    rooms[roomId].push(socket.id);

    socket.on('newOffer', newOffer => {
        offers[roomId].push({
            offererUserName: userName,
            offer: newOffer,
            offerIceCandidates: [],
            answererUserName: null,
            answer: null,
            answererIceCandidates: []
        });
        socket.broadcast.to(roomId).emit('newOfferAwaiting', offers[roomId].slice(-1));
    });

    socket.on('newAnswer', (offerObj, ackFunction) => {
        const socketToAnswer = connectedSockets[roomId].find(s => s.userName === offerObj.offererUserName);
        if (!socketToAnswer) {
            return;
        }
        const socketIdToAnswer = socketToAnswer.socketId;
        const offerToUpdate = offers[roomId].find(o => o.offererUserName === offerObj.offererUserName);
        if (!offerToUpdate) {
            return;
        }
        ackFunction(offerToUpdate.offerIceCandidates);
        offerToUpdate.answer = offerObj.answer;
        offerToUpdate.answererUserName = userName;
        socket.to(socketIdToAnswer).emit('answerResponse', offerToUpdate);
    });

    socket.on('sendIceCandidateToSignalingServer', iceCandidateObj => {
        const { didIOffer, iceUserName, iceCandidate } = iceCandidateObj;
        if (didIOffer) {
            const offerInOffers = offers[roomId].find(o => o.offererUserName === iceUserName);
            if (offerInOffers) {
                offerInOffers.offerIceCandidates.push(iceCandidate);
                if (offerInOffers.answererUserName) {
                    const socketToSendTo = connectedSockets[roomId].find(s => s.userName === offerInOffers.answererUserName);
                    if (socketToSendTo) {
                        socket.to(socketToSendTo.socketId).emit('receivedIceCandidateFromServer', iceCandidate);
                    }
                }
            }
        } else {
            const offerInOffers = offers[roomId].find(o => o.answererUserName === iceUserName);
            const socketToSendTo = connectedSockets[roomId].find(s => s.userName === offerInOffers.offererUserName);
            if (socketToSendTo) {
                socket.to(socketToSendTo.socketId).emit('receivedIceCandidateFromServer', iceCandidate);
            }
        }
    });

    socket.on('start-stream', () => {
        streamers[roomId] = socket.id;
        socket.to(roomId).broadcast.emit('new-streamer', socket.id);
    });

    socket.on('stop-stream', () => {
        if (streamers[roomId] === socket.id) {
            socket.to(roomId).broadcast.emit('streamer-disconnected');
            delete streamers[roomId];
        }
    });

    socket.on('disconnect', () => {
        connectedSockets[roomId] = connectedSockets[roomId].filter(s => s.socketId !== socket.id);
        rooms[roomId] = rooms[roomId].filter(id => id !== socket.id);

        if (streamers[roomId] === socket.id) {
            socket.to(roomId).broadcast.emit('streamer-disconnected');
            delete streamers[roomId];
        }
    });
});

app.get('/', (req, res) => {
    res.redirect(`/${uuidV4()}`);
});
