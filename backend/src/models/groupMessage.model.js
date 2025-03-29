import pool from '../lib/db.js';

class GroupMessage {
  static async sendMessage(groupId, senderId, text, image = null) {
    try {
      const [result] = await pool.execute(
        'INSERT INTO group_messages (group_id, sender_id, text, image) VALUES (?, ?, ?, ?)',
        [groupId, senderId, text, image]
      );
      
      const messageId = result.insertId;
      
      const [messages] = await pool.execute(`
        SELECT gm.*, 
               u.fullName AS sender_name,
               u.profilePic AS sender_profile_pic
        FROM group_messages gm
        JOIN users u ON gm.sender_id = u.id
        WHERE gm.id = ?
      `, [messageId]);
      
      const newMessage = messages[0];
      
      return newMessage;
    } catch (error) {
      console.error('Error in sendMessage:', error);
      throw error;
    }
  }
  
  static async getMessages(groupId, limit = 50) {
    try {
      const [rows] = await pool.execute(`
        SELECT gm.*, 
               u.fullName AS sender_name,
               u.profilePic AS sender_profile_pic
        FROM group_messages gm
        JOIN users u ON gm.sender_id = u.id
        WHERE gm.group_id = ?
        ORDER BY gm.created_at ASC
        LIMIT ?
      `, [groupId, limit]);
      
      return rows;
    } catch (error) {
      console.error('Error in getMessages:', error);
      throw error;
    }
  }
  
  static async deleteMessage(messageId, userId) {
    try {
      const [messages] = await pool.execute(
        'SELECT * FROM group_messages WHERE id = ? AND sender_id = ?',
        [messageId, userId]
      );
      
      if (messages.length === 0) {
        throw new Error('You can only delete your own messages');
      }
      
      await pool.execute(
        'DELETE FROM group_messages WHERE id = ?',
        [messageId]
      );
      
      return true;
    } catch (error) {
      console.error('Error in deleteMessage:', error);
      throw error;
    }
  }
}

export default GroupMessage;