'use strict';
require('dotenv').config();
 
const VERSION = '1.1.0';
 
// ---- Config ----
const PORT = parseInt(process.env.PORT || '3001', 10);
const MAX_CONNECTIONS = parseInt(process.env.MAX_CONNECTIONS || '10000', 10);
// Plusieurs origines possibles, séparées par des virgules
const CORS_ORIGINS = (process.env.CORS_ORIGIN || 'https://preprod.wayo.site')
  .split(',')
  .map(s => s.trim())
  .filter(Boolean);
// Clé optionnelle pour sécuriser /notify (envoyer ce header: x-api-key: <clé>)
const NOTIFY_TOKEN = process.env.NOTIFY_TOKEN || null;
 
// ---- App / IO ----
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
 
const app = express();
app.set('trust proxy', 1);           // utile derrière Nginx
app.use(express.json({ limit: '50mb' }));
 
const server = http.createServer(app);
const io = new Server(server, {
  cors: {
    origin: CORS_ORIGINS,
    methods: ['GET', 'POST'],
    credentials: true,
  },
  transports: ['websocket', 'polling'], // websocket prioritaire, polling en secours
  maxHttpBufferSize: 1e8,
  pingTimeout: 60_000,
  pingInterval: 25_000,
});
 
// ---- State ----
const meetingSubscriptions = new Map(); // meetingID -> Set(socketId)
const participantCounts = new Map();    // meetingID -> number
 
const getRoomSize = (room) => io.sockets.adapter.rooms.get(room)?.size || 0;
const clamp0 = (n) => Math.max(0, Number.isFinite(n) ? n : 0);
 
// ---- Health ----
app.get('/health', (_req, res) => {
  res.json({
    ok: true,
    version: VERSION,
    port: PORT,
    uptime_s: Math.round(process.uptime()),
    rooms: [...meetingSubscriptions.keys()],
  });
});
// ---- Notify (PHP → Node) ----
app.post('/notify', (req, res) => {
  try {
    if (NOTIFY_TOKEN) {
      const token = req.get('x-api-key');
      if (token !== NOTIFY_TOKEN) {
        return res.status(401).json({ status: 'error', message: 'Unauthorized' });
      }
    }
 
    const { meetingID, eventType, participantChange = 0, isRunning } = req.body || {};
    if (!meetingID || !eventType) {
      return res.status(400).json({ status: 'error', message: 'Missing meetingID or eventType' });
    }
 
    // Init
    if (!participantCounts.has(meetingID)) {
      participantCounts.set(meetingID, 0);
      meetingSubscriptions.set(meetingID, new Set());
    }
 
    // Update count
    let current = participantCounts.get(meetingID) || 0;
    if (eventType === 'user-joined') {
      current = clamp0(current + Number(participantChange || 1));
    } else if (eventType === 'user-left') {
      current = clamp0(current + Number(participantChange || -1)); // négatif = décrément
    } else if (eventType === 'meeting-ended') {
      current = 0;
      meetingSubscriptions.delete(meetingID);
      participantCounts.delete(meetingID);
      console.log(`[notify] meeting-ended -> cleanup ${meetingID}`);
    }
 
    if (eventType !== 'meeting-ended') {
      participantCounts.set(meetingID, current);
    }
 
    // Broadcast
    io.to(meetingID).emit('update_participants', {
      action: 'update_participants',
      meetingID,
      participantCount: current,
      isRunning: Boolean(isRunning ?? current > 0),
      ts: Date.now(),
    });
 
    return res.json({ status: 'success' });
  } catch (err) {
    console.error('[notify] error:', err);
    return res.status(500).json({ status: 'error', message: 'Internal error' });
  }
});

// ---- Socket.IO ----
io.on('connection', (socket) => {
  if (io.engine.clientsCount > MAX_CONNECTIONS) {
    console.warn(`[io] capacity reached (${MAX_CONNECTIONS}), rejecting ${socket.id}`);
    socket.emit('error', { message: 'Server at maximum capacity' });
    return socket.disconnect(true);
  }
 
  const ip = socket.handshake.headers['x-forwarded-for'] || socket.handshake.address;
  console.log(`[io] connected ${socket.id} from ${ip} | total=${io.engine.clientsCount}`);
 
  socket.on('subscribe', ({ meetingID }) => {
    if (!meetingID) {
      socket.emit('error', { message: 'Missing meetingID' });
      return;
    }
    socket.join(meetingID);
 
    if (!meetingSubscriptions.has(meetingID)) {
      meetingSubscriptions.set(meetingID, new Set());
      participantCounts.set(meetingID, 0);
    }
    meetingSubscriptions.get(meetingID).add(socket.id);
 
    const count = participantCounts.get(meetingID) || 0;
    socket.emit('current_state', {
      action: 'current_state',
      meetingID,
      participantCount: count,
      isRunning: count > 0,
      ts: Date.now(),
    });
 
    // Optionnel : informer les autres de la présence
    io.to(meetingID).emit('meeting:presence', {
      meetingID,
      sockets: getRoomSize(meetingID),
      ts: Date.now(),
    });
 
    console.log(`[io] ${socket.id} subscribed ${meetingID} | roomSockets=${getRoomSize(meetingID)}`);
  });
 
  socket.on('unsubscribe', ({ meetingID }) => {
    if (!meetingID) {
      socket.emit('error', { message: 'Missing meetingID' });
      return;
    }
    socket.leave(meetingID);
    const subs = meetingSubscriptions.get(meetingID);
    if (subs) {
      subs.delete(socket.id);
      if (subs.size === 0) {
        meetingSubscriptions.delete(meetingID);
        // On garde participantCounts si tu veux persister l’état,
        // sinon décommente la ligne ci-dessous :
       // participantCounts.delete(meetingID);
      }
    }
 
    io.to(meetingID).emit('meeting:presence', {
      meetingID,
      sockets: getRoomSize(meetingID),
      ts: Date.now(),
    });
 
    console.log(`[io] ${socket.id} unsubscribed ${meetingID} | roomSockets=${getRoomSize(meetingID)}`);
  });
 
  socket.on('request_current_state', ({ meetingID }) => {
    if (!meetingID) {
      socket.emit('error', { message: 'Missing meetingID' });
      return;
    }
    const count = participantCounts.get(meetingID) || 0;
    socket.emit('current_state', {
      action: 'current_state',
      meetingID,
      participantCount: count,
      isRunning: count > 0,
      ts: Date.now(),
    });
  });
 
  // Ping applicatif (évite le nom "ping" réservé Engine.IO)
  socket.on('app:ping', () => socket.emit('app:pong', { ts: Date.now() }));
 
  socket.on('disconnect', (reason) => {
    for (const [meetingID, subs] of meetingSubscriptions.entries()) {
      if (subs.delete(socket.id) && subs.size === 0) {
        meetingSubscriptions.delete(meetingID);
        // participantCounts.delete(meetingID); // optionnel
      }
    }
    console.log(`[io] disconnected ${socket.id} | reason=${reason}`);
  });
});
 
// ---- Robustesse ----
process.on('uncaughtException', (err) => {
  console.error('[fatal] uncaughtException:', err);
  // laissez tourner sous PM2, sauf si vous préférez redémarrer :
  // process.exit(1);
});
process.on('unhandledRejection', (reason, p) => {
  console.error('[fatal] unhandledRejection at:', p, 'reason:', reason);
  // process.exit(1);
});
 
// ---- Start (bind local uniquement) ----
server.listen(PORT, '127.0.0.1', () => {
  console.log(`Socket.IO server v${VERSION} on 127.0.0.1:${PORT}`);
  console.log(`Allowed origins: ${CORS_ORIGINS.join(', ')}`);
});
app.use('/bbb-hook', express.urlencoded({ extended: false }));
 
app.post('/bbb-hook', (req, res) => {
  try {
    const raw = req.body?.event;                      // BBB envoie form-urlencoded
    const msg = typeof raw === 'string' ? JSON.parse(raw) : raw;
    const type = msg?.data?.id;                       // 'user-joined' | 'user-left' | 'meeting-ended'
    const attrs = msg?.data?.attributes || {};
    const meetingID = attrs?.meeting?.['external-meeting-id'] || attrs?.meetingID;
 
    if (!meetingID || !type) return res.json({ status: 'ignored' });
 
    if (type === 'user-joined') {
      const cur = (participantCounts.get(meetingID) || 0) + 1;
      participantCounts.set(meetingID, cur);
      io.to(meetingID).emit('update_participants', { action:'update_participants', meetingID, participantCount:cur, isRunning:true, ts:Date.now() });
    } else if (type === 'user-left') {
      const cur = Math.max(0, (participantCounts.get(meetingID) || 0) - 1);
      participantCounts.set(meetingID, cur);
      io.to(meetingID).emit('update_participants', { action:'update_participants', meetingID, participantCount:cur, isRunning:cur>0, ts:Date.now() });
    } else if (type === 'meeting-ended') {
      participantCounts.delete(meetingID);
      meetingSubscriptions.delete(meetingID);
      io.to(meetingID).emit('update_participants', { action:'update_participants', meetingID, participantCount:0, isRunning:false, ts:Date.now() });
    } else if (type === 'meeting-created') {
      const cur = (participantCounts.get(meetingID) || 0) + 1;
      participantCounts.set(meetingID, cur);
      io.to(meetingID).emit('update_participants', { action:'update_participants', meetingID, participantCount:cur, isRunning:true, ts:Date.now() });
    }
 
    res.json({ status: 'ok' });
  } catch (e) {
    console.error('BBB hook error:', e);
    res.sendStatus(500);
  }
});