require('dotenv').config(); 
const PORT = process.env.PORT || 8080; 
const CORS_ORIGIN = process.env.CORS_ORIGIN || 'https://preprod.wayo.site:3001';
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');

const app = express();
app.use(express.json());
const server = http.createServer(app);
const io = new Server(server, {
  cors: {
    origin: CORS_ORIGIN,
    methods: ["GET", "POST"]
  },
  maxHttpBufferSize: 1e8,
  pingTimeout: 60000,
  pingInterval: 25000,
  transports: ['websocket'],
  maxConnections: 10000
});

const meetingSubscriptions = new Map();
const participantCounts = new Map();
const MAX_CONNECTIONS = 10000;

// New endpoint to handle notifications from PHP webhook
app.post('/notify', (req, res) => {
  const { meetingID, eventType, participantChange, isRunning } = req.body;
  if (!meetingID || !eventType) {
    console.warn('Notify endpoint - Missing meetingID or eventType');
    res.status(400).json({ status: 'error', message: 'Missing meetingID or eventType' });
    return;
  }

  console.log(`Notify received for meetingID: ${meetingID}, eventType: ${eventType}, participantChange: ${participantChange}, isRunning: ${isRunning}`);

  // Initialize participant count if not exists
  if (!participantCounts.has(meetingID)) {
    participantCounts.set(meetingID, 0);
    meetingSubscriptions.set(meetingID, new Set());
  }

  // Update participant count based on event type
  let currentCount = participantCounts.get(meetingID);
  if (eventType === 'user-joined') {
    currentCount += participantChange;
  } else if (eventType === 'user-left') {
    currentCount = Math.max(0, currentCount + participantChange);
  } else if (eventType === 'meeting-ended') {
    currentCount = 0;
    meetingSubscriptions.delete(meetingID);
    participantCounts.delete(meetingID);
    console.log(`Cleaned up meetingID: ${meetingID} as it is no longer running`);
  }

  // Update participant count
  if (eventType !== 'meeting-ended') {
    participantCounts.set(meetingID, currentCount);
  }

  // Broadcast update to all subscribers
  io.to(meetingID).emit('update_participants', {
    action: 'update_participants',
    meetingID,
    participantCount: currentCount,
    isRunning
  });

  res.status(200).json({ status: 'success', message: 'Notification processed' });
});

io.on('connection', (socket) => {
  if (io.engine.clientsCount > MAX_CONNECTIONS) {
    console.warn(`Max connections (${MAX_CONNECTIONS}) reached, rejecting client: ${socket.id}`);
    socket.emit('error', { message: 'Server at maximum capacity' });
    socket.disconnect(true);
    return;
  }

  console.log(`Client connected: ${socket.id}, Total clients: ${io.engine.clientsCount}`);

  socket.on('subscribe', (data) => {
    if (!data.meetingID) {
      console.warn(`Client ${socket.id} sent subscribe without meetingID`);
      socket.emit('error', { message: 'Missing meetingID' });
      return;
    }
    console.log(`Client ${socket.id} subscribed to meetingID: ${data.meetingID}`);
    socket.join(data.meetingID);
    if (!meetingSubscriptions.has(data.meetingID)) {
      meetingSubscriptions.set(data.meetingID, new Set());
      participantCounts.set(data.meetingID, 0); // Initialize to 0
    }
    meetingSubscriptions.get(data.meetingID).add(socket.id);

    // Emit current state immediately upon subscription
    const currentCount = participantCounts.get(data.meetingID) || 0;
    socket.emit('current_state', {
      action: 'current_state',
      meetingID: data.meetingID,
      participantCount: currentCount,
      isRunning: currentCount > 0
    });
  });

  socket.on('unsubscribe', (data) => {
    if (!data.meetingID) {
      console.warn(`Client ${socket.id} sent unsubscribe without meetingID`);
      socket.emit('error', { message: 'Missing meetingID' });
      return;
    }
    console.log(`Client ${socket.id} unsubscribed from meetingID: ${data.meetingID}`);
    socket.leave(data.meetingID);
    const subscribers = meetingSubscriptions.get(data.meetingID);
    if (subscribers) {
      subscribers.delete(socket.id);
      if (subscribers.size === 0) {
        meetingSubscriptions.delete(data.meetingID);
        participantCounts.delete(data.meetingID);
        console.log(`No more subscribers for meetingID: ${data.meetingID}, cleaned up`);
      }
    }
  });

  socket.on('request_current_state', (data) => {
    if (!data.meetingID) {
      console.warn(`Client ${socket.id} sent request_current_state without meetingID`);
      socket.emit('error', { message: 'Missing meetingID' });
      return;
    }

    const count = participantCounts.get(data.meetingID) || 0;
    const isRunning = meetingSubscriptions.has(data.meetingID) && count > 0;

    socket.emit('current_state', {
      action: 'current_state',
      meetingID: data.meetingID,
      participantCount: count,
      isRunning
    });
    console.log(`Sent current state for meetingID: ${data.meetingID}, count: ${count}, isRunning: ${isRunning}`);
  });

  socket.on('disconnect', (reason) => {
    console.log(`Client ${socket.id} disconnected: ${reason}`);
    for (const [meetingID, subscribers] of meetingSubscriptions.entries()) {
      if (subscribers.has(socket.id)) {
        subscribers.delete(socket.id);
        if (subscribers.size === 0) {
          meetingSubscriptions.delete(meetingID);
          participantCounts.delete(meetingID);
          console.log(`No more subscribers for meetingID: ${meetingID}, cleaned up`);
        }
      }
    }
  });

  socket.on('ping', () => {
    socket.emit('pong');
  });
});

process.on('uncaughtException', (err) => { 

console.error('Uncaught Exception:', err); 

  process.exit(1); 

}); 

process.on('unhandledRejection', (reason, promise) => { 

console.error('Unhandled Rejection at:', promise, 'reason:', reason); 

  process.exit(1); 

});

server.listen(PORT, () => {
  console.log(`Socket.IO server running on port ${PORT}`);
});