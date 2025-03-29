import pool from '../lib/db.js';

export const createMessage = async ({ senderId, receiverId, text, image }) => {
  const [result] = await pool.execute(
    'INSERT INTO messages (senderId, receiverId, text, image) VALUES (?, ?, ?, ?)',
    [senderId, receiverId, text || null, image || null]
  );
  
  const [rows] = await pool.execute(
    'SELECT * FROM messages WHERE id = ?',
    [result.insertId]
  );
  
  return rows[0];
};

export const getMessagesByUsers = async (myId, userToChatId) => {
  const [rows] = await pool.execute(
    'SELECT * FROM messages WHERE (senderId = ? AND receiverId = ?) OR (senderId = ? AND receiverId = ?) ORDER BY createdAt ASC',
    [myId, userToChatId, userToChatId, myId]
  );
  return rows;
};