import express from 'express';
import http from 'http';
import { Server } from 'socket.io';
import cors from 'cors';

const app = express();
const server = http.createServer(app);

const io = new Server(server, {
  cors: {
    origin: "http://localhost:*", 
    methods: ["GET", "POST"],
    credentials: true, 
  },
});

const userSocketMap = {}; 
const userGroupMap = {}; 

export function getReceiverSocketId(userId) {
  return userSocketMap[userId];
}

io.on('connection', (socket) => {
  console.log('A user connected:', socket.id);
  
  const userId = socket.handshake.query.userId;
  if (userId) {
    userSocketMap[userId] = socket.id;
    userGroupMap[userId] = userGroupMap[userId] || [];
    
    io.emit('getOnlineUsers', Object.keys(userSocketMap));
  }

  socket.on('joinGroup', ({ groupId }) => {
    if (!groupId) {
      console.error('No groupId provided for joinGroup event');
      return;
    }
    
    console.log(`User ${userId} (socket ${socket.id}) joined group ${groupId}`);
    socket.join(`group:${groupId}`);
    
    if (userId) {
      if (!userGroupMap[userId]) userGroupMap[userId] = [];
      if (!userGroupMap[userId].includes(groupId)) {
        userGroupMap[userId].push(groupId);
      }
    }
  });
    
  socket.on('leaveGroup', ({ groupId }) => {
    if (!groupId) {
      console.error('No groupId provided for leaveGroup event');
      return;
    }
    
    console.log(`User ${userId} (socket ${socket.id}) left group ${groupId}`);
    socket.leave(`group:${groupId}`);
    
    if (userId && userGroupMap[userId]) {
      userGroupMap[userId] = userGroupMap[userId].filter(id => id !== groupId);
    }
  });
  
  socket.on('disconnect', () => {
    console.log('A user disconnected:', socket.id);
    if (userId) {
      delete userSocketMap[userId];
      delete userGroupMap[userId];
      io.emit('getOnlineUsers', Object.keys(userSocketMap));
    }
  });
});

export function broadcastGroupMessage(message) {
  if (!message || !message.group_id) {
    console.error('Invalid message object for broadcasting:', message);
    return;
  }
  
  console.log(`Broadcasting message to group:${message.group_id}`, message);
  io.to(`group:${message.group_id}`).emit('groupMessage', message);
}

export function broadcastPrivateMessage(message) {
  if (!message || !message.senderId || !message.receiverId) {
    console.error('Invalid private message object for broadcasting:', message);
    return;
  }
  
  const receiverSocketId = getReceiverSocketId(message.receiverId);
  if (receiverSocketId) {
    io.to(receiverSocketId).emit('newMessage', message);
  }
}

export { io, app, server };