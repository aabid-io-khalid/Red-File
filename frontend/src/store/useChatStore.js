import { create } from 'zustand';
import toast from 'react-hot-toast';
import { axiosInstance } from '../lib/axios';
import { useAuthStore } from './useAuthStore';

const normalizeTimestamp = (timestamp) => {
  if (!timestamp) return new Date().toISOString();
  const date = new Date(timestamp);
  return isNaN(date.getTime()) ? new Date().toISOString() : date.toISOString();
};

export const useChatStore = create((set, get) => ({
  messages: [],
  users: [],
  selectedUser: null,
  isUsersLoading: false,
  isMessagesLoading: false,

  getUsers: async () => {
    set({ isUsersLoading: true });
    try {
      const res = await axiosInstance.get('/messages/users');
      set({ users: res.data });
    } catch (error) {
      toast.error(error.response?.data?.message || 'Failed to load users');
    } finally {
      set({ isUsersLoading: false });
    }
  },

  getMessages: async (userId) => {
    set({ isMessagesLoading: true });
    try {
      const res = await axiosInstance.get(`/messages/${userId}`);
      const normalizedMessages = res.data.map(msg => ({
        ...msg,
        created_at: normalizeTimestamp(msg.created_at),
      }));
      set({ messages: normalizedMessages });
    } catch (error) {
      toast.error(error.response?.data?.message || 'Failed to load messages');
    } finally {
      set({ isMessagesLoading: false });
    }
  },

  sendMessage: async ({ text, image, receiverId }) => {
    const { selectedUser, messages } = get();
    const actualReceiverId = receiverId || selectedUser?.id;
    if (!actualReceiverId) {
      toast.error('No user selected');
      return;
    }

    try {
      const res = await axiosInstance.post(`/messages/send/${actualReceiverId}`, { text, image });
      const newMessage = {
        ...res.data,
        created_at: normalizeTimestamp(res.data.created_at),
      };
      set({ messages: [...messages, newMessage] });
      return newMessage;
    } catch (error) {
      toast.error(error.response?.data?.message || 'Failed to send message');
      throw error;
    }
  },

  subscribeToMessages: () => {
    const { selectedUser } = get();
    if (!selectedUser) return;

    const socket = useAuthStore.getState().socket;
    if (!socket) {
      console.error('Socket not available');
      return;
    }

    get().unsubscribeFromMessages();

    socket.on('newMessage', (newMessage) => {
      const isMessageFromSelectedUser =
        newMessage.senderId === selectedUser.id ||
        newMessage.receiverId === selectedUser.id;

      if (!isMessageFromSelectedUser) return;

      const normalizedMessage = {
        ...newMessage,
        created_at: normalizeTimestamp(newMessage.created_at),
      };
      set({
        messages: [...get().messages, normalizedMessage],
      });
    });
  },

  unsubscribeFromMessages: () => {
    const socket = useAuthStore.getState().socket;
    if (socket) {
      socket.off('newMessage');
    }
  },

  setSelectedUser: (user) => {
    set({ selectedUser: user });
  },

  resetMessages: () => {
    set({ messages: [] });
  },

  updateMessageStatus: async (messageId, status) => {
    try {
      await axiosInstance.post(`/messages/${messageId}/status`, { status });
      set({
        messages: get().messages.map(msg =>
          msg.id === messageId ? { ...msg, status } : msg
        ),
      });
    } catch (error) {
      console.error('Failed to update message status:', error);
    }
  },
}));