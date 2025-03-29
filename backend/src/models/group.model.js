import pool from '../lib/db.js';

class Group {
  static async createGroup(name, creatorId) {
    try {
      const [groupResult] = await pool.execute(
        'INSERT INTO groups (name, creator_id) VALUES (?, ?)',
        [name, creatorId]
      );
      
      const groupId = groupResult.insertId;
      
      await pool.execute(
        'INSERT INTO group_members (group_id, user_id) VALUES (?, ?)',
        [groupId, creatorId]
      );
      
      const [groups] = await pool.execute(
        'SELECT g.*, ? AS creator_id, 1 AS is_admin FROM groups g WHERE g.id = ?',
        [creatorId, groupId]
      );
      
      return groups[0];
    } catch (error) {
      console.error('Error in createGroup:', error);
      throw error;
    }
  }
  
  static async getAllGroups() {
    try {
      const [groups] = await pool.execute(`
        SELECT g.id, g.name, g.created_at, g.creator_id, COUNT(gm.id) AS memberCount
        FROM groups g
        LEFT JOIN group_members gm ON g.id = gm.group_id
        GROUP BY g.id
        ORDER BY g.created_at DESC
      `);
      
      return groups;
    } catch (error) {
      console.error('Error in getAllGroups:', error);
      throw error;
    }
  }
  
  static async addMember(groupId, userId) {
    try {
      const [existingMembers] = await pool.execute(
        'SELECT * FROM group_members WHERE group_id = ? AND user_id = ?',
        [groupId, userId]
      );
      
      if (existingMembers.length === 0) {
        await pool.execute(
          'INSERT INTO group_members (group_id, user_id) VALUES (?, ?)',
          [groupId, userId]
        );
      }
      
      return true;
    } catch (error) {
      console.error('Error in addMember:', error);
      throw error;
    }
  }
  
  static async removeMember(groupId, userId) {
    try {
      await pool.execute(
        'DELETE FROM group_members WHERE group_id = ? AND user_id = ?',
        [groupId, userId]
      );
      
      return true;
    } catch (error) {
      console.error('Error in removeMember:', error);
      throw error;
    }
  }
  
  static async isUserMember(groupId, userId) {
    try {
      const [members] = await pool.execute(
        'SELECT * FROM group_members WHERE group_id = ? AND user_id = ?',
        [groupId, userId]
      );
      
      return members.length > 0;
    } catch (error) {
      console.error('Error in isUserMember:', error);
      throw error;
    }
  }
  
  static async isUserCreator(groupId, userId) {
    try {
      const [rows] = await pool.execute(
        'SELECT 1 FROM groups WHERE id = ? AND creator_id = ?',
        [groupId, userId]
      );
      
      return rows.length > 0;
    } catch (error) {
      console.error('Error in isUserCreator:', error);
      throw error;
    }
  }
  
  static async deleteGroup(groupId) {
    try {
      await pool.execute('DELETE FROM group_members WHERE group_id = ?', [groupId]);
      await pool.execute('DELETE FROM group_messages WHERE group_id = ?', [groupId]);
      await pool.execute('DELETE FROM groups WHERE id = ?', [groupId]);
    } catch (error) {
      console.error('Error in deleteGroup:', error);
      throw error;
    }
  }
  
  static async getGroupsForUser(userId) {
    try {
      const [groups] = await pool.execute(`
        SELECT g.id, g.name, g.created_at, g.creator_id,
               COUNT(gm2.id) AS memberCount,
               CASE WHEN g.creator_id = ? THEN 1 ELSE 0 END AS is_admin
        FROM groups g
        JOIN group_members gm ON g.id = gm.group_id AND gm.user_id = ?
        LEFT JOIN group_members gm2 ON g.id = gm2.group_id
        GROUP BY g.id
        ORDER BY g.created_at DESC
      `, [userId, userId]);
      
      return groups;
    } catch (error) {
      console.error('Error in getGroupsForUser:', error);
      throw error;
    }
  }
  
  static async getGroupMembers(groupId) {
    try {
      const [members] = await pool.execute(`
        SELECT u.id, u.fullName AS name, u.profilePic,
               CASE WHEN g.creator_id = u.id THEN 1 ELSE 0 END AS is_admin
        FROM group_members gm
        JOIN users u ON gm.user_id = u.id
        JOIN groups g ON gm.group_id = g.id
        WHERE gm.group_id = ?
      `, [groupId]);
      
      return members;
    } catch (error) {
      console.error('Error in getGroupMembers:', error);
      throw error;
    }
  }
}

export default Group;