import pool from '../lib/db.js';

export const createUser = async (userData) => {
  const { fullName, email, password } = userData;
  const [result] = await pool.execute(
    'INSERT INTO users (fullName, email, password) VALUES (?, ?, ?)',
    [fullName, email, password]
  );
  return { id: result.insertId, fullName, email, password }; 
};

export const findUserByEmail = async (email) => {
  const [rows] = await pool.execute('SELECT * FROM users WHERE email = ?', [email]);
  return rows[0]; 
};

export const updateUserProfilePic = async (userId, profilePic) => {
  await pool.execute('UPDATE users SET profilePic = ? WHERE id = ?', [profilePic, userId]);
};

export const findUserById = async (userId) => {
  const [rows] = await pool.execute('SELECT * FROM users WHERE id = ?', [userId]);
  return rows[0] || null;
};

export const upsertUser = async ({ id, name, email, profilePic }) => {
  await pool.execute(
    'INSERT INTO users (id, fullName, email, profilePic) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE fullName = ?, email = ?, profilePic = ?',
    [id, name, email, profilePic || null, name, email, profilePic || null]
  );
  const [rows] = await pool.execute('SELECT * FROM users WHERE id = ?', [id]);
  return rows[0];
};

export default {
  createUser,
  findUserByEmail,
  findUserById,
  updateUserProfilePic, 
  upsertUser,   
};
