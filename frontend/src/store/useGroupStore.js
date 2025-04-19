import { create } from 'zustand';
import { axiosInstance } from '../lib/axios';
import { io } from 'socket.io-client';

let socket = null;

export const useGroupStore = create((set, get) => ({
  userGroups: [],
  currentGroupMessages: [],
  availableGroups: [],
  currentGroup: null,
  isLoading: false,
  error: null,
  currentUserId: null,
  groupMembers: {},

  initializeSocket: (userId) => {
    if (!userId) {
      console.error("Cannot initialize socket without userId");
      return null;
    }
    
    const socketUrl = "http://localhost:5001";

    if (socket && socket.connected) {
      console.log("Socket already initialized and connected:", socket.id);
      return socket;
    }
    
    if (socket) {
      socket.disconnect();
      console.log("Disconnecting existing socket before reconnecting");
    }

    socket = io(socketUrl, {
      withCredentials: true,
      transports: ["websocket", "polling"],
      query: { userId },
      reconnection: true,
      reconnectionAttempts: 5,
      reconnectionDelay: 1000,
    });

    socket.on("connect", () => {
      console.log("Group socket connected:", socket.id);
      
      const { currentGroup } = get();
      if (currentGroup) {
        socket.emit('joinGroup', { groupId: currentGroup.id });
      }
    });

    socket.on("connect_error", (err) => {
      console.error("Group socket connection error:", err.message);
    });

    socket.on('groupMessage', (message) => {
      const { currentGroupMessages, currentGroup } = get();
      if (currentGroup && message.group_id === currentGroup.id) {
        set({
          currentGroupMessages: [...currentGroupMessages, message],
        });
      }
    });

    socket.on('groupUserStatus', ({ groupId, userId, status }) => {
      const { groupMembers } = get();
      if (groupMembers[groupId]) {
        set({
          groupMembers: {
            ...groupMembers,
            [groupId]: groupMembers[groupId].map(member => 
              member.id === userId ? { ...member, online: status === 'online' } : member
            )
          }
        });
      }
    });

    return socket;
  },

  setCurrentUserId: (userId) => {
    set({ currentUserId: userId });
    get().initializeSocket(userId);
  },

  joinGroupChannel: (groupId) => {
    if (!groupId) return;
    
    if (socket && socket.connected) {
      socket.emit('joinGroup', { groupId });
      console.log(`Joined group channel: ${groupId}`);
    } else {
      console.error("Socket not connected, cannot join group:", groupId);
      const { currentUserId } = get();
      if (currentUserId) {
        get().initializeSocket(currentUserId);
      }
    }
  },

  leaveGroupChannel: (groupId) => {
    if (!groupId) return;
    
    if (socket && socket.connected) {
      socket.emit('leaveGroup', { groupId });
      console.log(`Left group channel: ${groupId}`);
    } else {
      console.error("Socket not connected, cannot leave group:", groupId);
    }
  },

  getUserGroups: async () => {
    set({ isLoading: true, error: null });
    try {
      const response = await axiosInstance.get('/groups');
      set({ userGroups: response.data, isLoading: false });
      return response.data;
    } catch (error) {
      console.error('Error fetching user groups:', error);
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  getAllGroups: async () => {
    set({ isLoading: true, error: null });
    try {
      const response = await axiosInstance.get('/groups/available');
      set({ availableGroups: response.data, isLoading: false });
      return response.data;
    } catch (error) {
      console.error('Error fetching available groups:', error);
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  getGroupMessages: async (groupId) => {
    if (!groupId) {
      console.error("No groupId provided to getGroupMessages");
      return;
    }
    
    set({ isLoading: true, error: null });
    try {
      const response = await axiosInstance.get(`/groups/${groupId}/messages`);
      
      const groupDetails = get().userGroups.find(g => g.id === parseInt(groupId)) || 
                          get().availableGroups.find(g => g.id === parseInt(groupId));
      
      set({
        currentGroupMessages: response.data,
        currentGroup: groupDetails || { id: groupId },
        isLoading: false,
      });
      
      get().joinGroupChannel(groupId);
      
      get().getGroupMembersData(groupId);
      
      return response.data;
    } catch (error) {
      console.error('Error fetching group messages:', error);
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  getGroupMembersData: async (groupId) => {
    if (!groupId) return;
    
    try {
      const response = await axiosInstance.get(`/groups/${groupId}/members`);
      set({
        groupMembers: {
          ...get().groupMembers,
          [groupId]: response.data
        }
      });
      return response.data;
    } catch (error) {
      console.error('Error fetching group members:', error);
      throw error;
    }
  },

  sendGroupMessage: async (groupId, text, image = null) => {
    if (!groupId) {
      console.error("No groupId provided to sendGroupMessage");
      return;
    }
    
    try {
      const response = await axiosInstance.post(`/groups/${groupId}/messages`, { text, image });
      
      return response.data;
    } catch (error) {
      console.error('Error sending group message:', error);
      throw error;
    }
  },

  clearCurrentGroup: () => {
    const { currentGroup } = get();
    if (currentGroup) {
      get().leaveGroupChannel(currentGroup.id);
    }
    set({
      currentGroupMessages: [],
      currentGroup: null,
    });
  },

  createGroup: async (name) => {
    set({ isLoading: true, error: null });
    try {
      const response = await axiosInstance.post('/groups', { name });
      set({ isLoading: false });
      return response.data;
    } catch (error) {
      console.error('Error creating group:', error);
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  joinGroup: async (groupId) => {
    set({ isLoading: true, error: null });
    try {
      const response = await axiosInstance.post(`/groups/${groupId}/join`);
      set({ isLoading: false });
      return response.data;
    } catch (error) {
      console.error('Error joining group:', error);
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },
  
  leaveGroup: async (groupId) => {
    set({ isLoading: true, error: null });
    try {
      get().leaveGroupChannel(groupId);
      
      const response = await axiosInstance.post(`/groups/${groupId}/leave`);
      
      const { currentGroup } = get();
      if (currentGroup && currentGroup.id === groupId) {
        get().clearCurrentGroup();
      }
      
      set({ isLoading: false });
      return response.data;
    } catch (error) {
      console.error('Error leaving group:', error);
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },
  
  deleteGroup: async (groupId) => {
    set({ isLoading: true, error: null });
    try {
      get().leaveGroupChannel(groupId);
      
      const response = await axiosInstance.delete(`/groups/${groupId}`);
      
      const { currentGroup } = get();
      if (currentGroup && currentGroup.id === groupId) {
        get().clearCurrentGroup();
      }
      
      set({ isLoading: false });
      return response.data;
    } catch (error) {
      console.error('Error deleting group:', error);
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },
  
  cleanup: () => {
    const { currentGroup } = get();
    if (currentGroup) {
      get().leaveGroupChannel(currentGroup.id);
    }
    
  
  }
}));