import { upsertUser, findUserById } from '../models/user.model.js';
import jwt from 'jsonwebtoken';

const JWT_SECRET = 'secret_key';

export const protectRoute = async (req, res, next) => {
  try {
    const token = req.query.token || req.cookies.jwt || req.headers['authorization']?.split(' ')[1];
    console.log('Middleware - Received token:', token);

    if (!token) {
      console.log('Middleware - No token provided');
      return res.status(401).json({ message: 'Unauthorized - No Token Provided' });
    }

    let decoded;
    try {
      decoded = JSON.parse(Buffer.from(token, 'base64').toString('utf-8'));
      console.log('Middleware - Decoded Base64 token:', decoded);
      const tokenAge = Math.floor(Date.now() / 1000) - decoded.timestamp;
      if (tokenAge > 3600) {
        console.log('Middleware - Token expired');
        return res.status(401).json({ message: 'Unauthorized - Token Expired' });
      }
    } catch (base64Error) {
      try {
        decoded = jwt.verify(token, JWT_SECRET);
        console.log('Middleware - Decoded JWT token:', decoded);
      } catch (jwtError) {
        console.log('Middleware - Token decode failed:', { base64Error, jwtError });
        return res.status(401).json({ message: 'Unauthorized - Invalid Token Format' });
      }
    }

    let user;
    if (decoded.timestamp) {
      user = await upsertUser({
        id: decoded.id,
        name: decoded.name,
        email: decoded.email,
        profilePic: decoded.profilePic || null,
      });
    } else {
      user = await findUserById(decoded.userId);
    }

    if (!user) {
      console.log('Middleware - User not found');
      return res.status(404).json({ message: 'User not found' });
    }

    console.log('Middleware - User authenticated:', user);
    req.user = user;
    next();
  } catch (error) {
    console.error('Middleware - Error:', error.message);
    res.status(401).json({ message: 'Unauthorized - Invalid Token' });
  }
};