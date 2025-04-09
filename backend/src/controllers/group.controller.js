import Group from '../models/group.model.js';
import GroupMessage from '../models/groupMessage.model.js';
import { broadcastGroupMessage } from '../lib/socket.js';

export const createGroup = async (req, res) => {
  const { name } = req.body;
  const creatorId = req.user.id;

  try {
    const newGroup = await Group.createGroup(name, creatorId);
    res.status(201).json(newGroup);
  } catch (error) {
    console.error("Error creating group:", error);
    res.status(500).json({ error: "Internal server error" });
  }
};

export const getAllGroups = async (req, res) => {
  try {
    const groups = await Group.getAllGroups();
    res.status(200).json(groups);
  } catch (error) {
    console.error("Error retrieving all groups:", error);
    res.status(500).json({ error: "Internal server error" });
  }
};

export const joinGroup = async (req, res) => {
  const { groupId } = req.params;
  const userId = req.user.id;

  try {
    const isMember = await Group.isUserMember(groupId, userId);
    if (isMember) {
      return res.status(400).json({ error: "You are already a member of this group" });
    }
    
    await Group.addMember(groupId, userId);
    res.status(200).json({ message: "Successfully joined the group" });
  } catch (error) {
    console.error("Error joining group:", error);
    res.status(500).json({ error: "Internal server error" });
  }
};

export const leaveGroup = async (req, res) => {
  const { groupId } = req.params;
  const userId = req.user.id;

  try {
    const isMember = await Group.isUserMember(groupId, userId);
    if (!isMember) {
      return res.status(400).json({ error: "You are not a member of this group" });
    }
    
    await Group.removeMember(groupId, userId);
    res.status(200).json({ message: "Successfully left the group" });
  } catch (error) {
    console.error("Error leaving group:", error);
    res.status(500).json({ error: "Internal server error" });
  }
};

export const deleteGroup = async (req, res) => {
  const { groupId } = req.params;
  const userId = req.user.id;

  try {
    const isCreator = await Group.isUserCreator(groupId, userId);
    if (!isCreator) {
      return res.status(403).json({ error: "Only the group creator can delete the group" });
    }
    
    await Group.deleteGroup(groupId);
    res.status(200).json({ message: "Group successfully deleted" });
  } catch (error) {
    console.error("Error deleting group:", error);
    res.status(500).json({ error: "Internal server error" });
  }
};

export const sendMessageToGroup = async (req, res) => {
  const { groupId } = req.params;
  const { text, image } = req.body;
  const senderId = req.user.id;

  console.log(`Sending message to group ${groupId} by user ${senderId}`);

  try {
    console.log("Checking group membership...");
    const isMember = await Group.isUserMember(groupId, senderId);
    console.log(`Is user a member? ${isMember}`);
    if (!isMember) {
      return res.status(403).json({ error: "You must be a member of the group to send messages" });
    }
    
    console.log("Sending message to group...");
    const newMessage = await GroupMessage.sendMessage(groupId, senderId, text, image);
    console.log("Message sent:", newMessage);

    console.log("Broadcasting message...");
    broadcastGroupMessage(newMessage);

    res.status(201).json(newMessage);
  } catch (error) {
    console.error("Error sending message:", error);
    res.status(500).json({ error: "Internal server error" });
  }
};

export const getGroupMessages = async (req, res) => {
  const { groupId } = req.params;
  const userId = req.user.id;

  try {
    const isMember = await Group.isUserMember(groupId, userId);
    if (!isMember) {
      return res.status(403).json({ error: "You must be a member of the group to view messages" });
    }
    
    const messages = await GroupMessage.getMessages(groupId);
    res.status(200).json(messages);
  } catch (error) {
    console.error("Error retrieving messages:", error);
    res.status(500).json({ error: "Internal server error" });
  }
};

export const getGroupsForUser = async (req, res) => {
  const userId = req.user.id;

  try {
    const groups = await Group.getGroupsForUser(userId);
    res.status(200).json(groups);
  } catch (error) {
    console.error("Error retrieving groups:", error);
    res.status(500).json({ error: "Internal server error" });
  }
};

export const getGroupMembers = async (req, res) => {
  const { groupId } = req.params;
  const userId = req.user.id;

  try {
    const isMember = await Group.isUserMember(groupId, userId);
    if (!isMember) {
      return res.status(403).json({ error: "You must be a member of the group to view members" });
    }
    
    const members = await Group.getGroupMembers(groupId);
    res.status(200).json(members);
  } catch (error) {
    console.error("Error retrieving group members:", error);
    res.status(500).json({ error: "Internal server error" });
  }
};