const fs = require('fs');
const https = require('https');
const express = require('express');
const app = express();
const socketio = require('socket.io');
app.use(express.static(__dirname));

const key = fs.readFileSync('cert.key');
const cert = fs.readFileSync('cert.crt');

const expressServer = https.createServer({ key, cert }, app);
const io = socketio(expressServer, {
    cors: {
        origin: ["https://localhost", "https://10.54.3.61"],
        methods: ["GET", "POST"]
    }
});
expressServer.listen(8181);

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

    socket.on('disconnect', () => {
        connectedSockets[roomId] = connectedSockets[roomId].filter(s => s.socketId !== socket.id);
    });
});
