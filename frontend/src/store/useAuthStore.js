import { create } from 'zustand';
import { axiosInstance, setAuthToken } from '../lib/axios.js';
import toast from 'react-hot-toast';
import { io } from 'socket.io-client';

const BASE_URL = import.meta.env.MODE === 'development' ? 'http://localhost:5001' : '/';

export const useAuthStore = create((set, get) => ({
  authUser: null,
  isSigningUp: false,
  isLoggingIn: false,
  isUpdatingProfile: false,
  isCheckingAuth: true, 
  onlineUsers: [],
  socket: null,

  checkAuth: async () => {
    const token = localStorage.getItem('token');
    console.log('Checking auth with token from localStorage:', token);
    if (!token) {
      set({ authUser: null, isCheckingAuth: false });
      return;
    }

    setAuthToken(token);
    try {
      const res = await axiosInstance.get('/auth/check');
      set({ authUser: res.data });
      get().connectSocket();
    } catch (error) {
      console.log('Error in checkAuth:', error);
      set({ authUser: null });
    } finally {
      set({ isCheckingAuth: false });
    }
  },

  signup: async (data) => {
    set({ isSigningUp: true });
    try {
      const res = await axiosInstance.post('/auth/signup', data);
      const token = res.data.token || localStorage.getItem('token');
      if (token) {
        localStorage.setItem('token', token);
        setAuthToken(token);
      }
      set({ authUser: res.data });
      toast.success('Account created successfully');
      get().connectSocket();
    } catch (error) {
      toast.error(error.response?.data?.message || 'Signup failed');
    } finally {
      set({ isSigningUp: false });
    }
  },

  login: async (data) => {
    set({ isLoggingIn: true });
    try {
      const res = await axiosInstance.post('/auth/login', data);
      const token = res.data.token || localStorage.getItem('token');
      if (token) {
        localStorage.setItem('token', token);
        setAuthToken(token);
      }
      set({ authUser: res.data });
      toast.success('Logged in successfully');
      get().connectSocket();
    } catch (error) {
      toast.error(error.response?.data?.message || 'Login failed');
    } finally {
      set({ isLoggingIn: false });
    }
  },

  logout: async () => {
    try {
      await axiosInstance.post('/auth/logout');
      set({
        authUser: null,
        onlineUsers: [],
        socket: null, 
      });
      localStorage.removeItem('token');
      setAuthToken(null);
      get().disconnectSocket();
      toast.success('Logged out successfully');
    } catch (error) {
      toast.error(error.response?.data?.message || 'Logout failed');
    }
  },

  updateProfile: async (data) => {
    set({ isUpdatingProfile: true });
    try {
      const res = await axiosInstance.put('/auth/update-profile', data);
      set({ authUser: { ...get().authUser, profilePic: res.data.profilePic } });
      toast.success('Profile updated successfully');
    } catch (error) {
      toast.error(error.response?.data?.message || 'Profile update failed');
    } finally {
      set({ isUpdatingProfile: false });
    }
  },

  setAuthFromLaravel: async (token) => {
    console.log('setAuthFromLaravel - Received token:', token);
    set({ isCheckingAuth: true }); 
    try {
      const decoded = JSON.parse(atob(token));
      console.log('setAuthFromLaravel - Decoded token:', decoded);
      if (Math.floor(Date.now() / 1000) - decoded.timestamp > 3600) {
        throw new Error('Token expired');
      }
      localStorage.setItem('token', token);
      setAuthToken(token);
      const newUser = {
        id: decoded.id,
        name: decoded.name,
        email: decoded.email,
        profilePic: decoded.profilePic || null,
      };
      set({ authUser: newUser, isCheckingAuth: false }); 
      get().connectSocket();
      console.log('setAuthFromLaravel - Success:', { token, user: newUser });
      return newUser;
    } catch (error) {
      console.error('setAuthFromLaravel - Error:', error.message);
      toast.error('Invalid or expired token from Laravel');
      set({ authUser: null, isCheckingAuth: false }); 
      localStorage.removeItem('token');
      throw error;
    }
  },

  connectSocket: () => {
    const { authUser } = get();
    if (!authUser || get().socket?.connected) return;
  
    const socket = io(BASE_URL, {
      query: { userId: authUser.id },
    });
    socket.connect();
    set({ socket });
    socket.on('connect', () => {
      console.log('Socket connected for user:', authUser.id);
    });
    socket.on('getOnlineUsers', (userIds) => {
      console.log('Socket - Online users:', userIds);
      set({ onlineUsers: userIds });
    });
  },

  disconnectSocket: () => {
    const socket = get().socket;
    if (socket?.connected) {
      socket.disconnect();
      set({ socket: null });
    }
  },
}));