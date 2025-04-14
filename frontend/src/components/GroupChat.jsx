import React, { useEffect, useState, useRef } from 'react';
import { useGroupStore } from '../store/useGroupStore';
import { useAuthStore } from '../store/useAuthStore';
import { Send, ArrowLeft, Users, Trash2 } from 'lucide-react';
import toast from 'react-hot-toast';

const GroupChat = ({ groupId, userId, onBack }) => {
  const { 
    getGroupMessages,
    sendGroupMessage, 
    currentGroupMessages, 
    userGroups,
    isLoading,
    initializeSocket,
    joinGroupChannel,
    leaveGroupChannel,
    clearCurrentGroup,
    deleteGroup
  } = useGroupStore();
  
  const { authUser } = useAuthStore();
  const [message, setMessage] = useState('');
  const messagesEndRef = useRef(null);
  
  const currentGroup = userGroups.find(group => group.id === groupId) || {};
  const isAdmin = currentGroup.is_admin;

  useEffect(() => {
    const setupChat = async () => {
      if (userId && groupId) {
        const cleanup = initializeSocket(userId);
        
        await getGroupMessages(groupId);
        joinGroupChannel(groupId);

        return () => {
          leaveGroupChannel(groupId);
          clearCurrentGroup();
          cleanup();
        };
      }
    };

    const cleanupFn = setupChat();

    return () => {
      if (cleanupFn) cleanupFn.then(fn => fn && fn());
    };
  }, [groupId, userId, getGroupMessages, joinGroupChannel, leaveGroupChannel, initializeSocket, clearCurrentGroup]);

  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [currentGroupMessages]);

  const handleSendMessage = async (e) => {
    e.preventDefault();
    if (!message.trim()) return;
    
    try {
      await sendGroupMessage(groupId, message.trim());
      setMessage('');
    } catch (error) {
      console.error('Error sending message:', error);
    }
  };

  const handleDeleteGroup = async () => {
    if (window.confirm('Are you sure you want to delete this group? This action cannot be undone.')) {
      try {
        await deleteGroup(groupId);
        toast.success('Group deleted successfully!');
        onBack(); 
      } catch (error) {
        toast.error(error.response?.data?.error || 'Error deleting group');
      }
    }
  };

  const formatMessageTime = (timestamp) => {
    return new Date(timestamp).toLocaleTimeString([], {
      hour: '2-digit', 
      minute: '2-digit'
    });
  };

  return (
    <div className="flex flex-col h-full w-full">
      <div className="bg-base-200 p-4 flex items-center space-x-4 border-b">
        <button 
          className="btn btn-sm btn-ghost" 
          onClick={onBack}
        >
          <ArrowLeft size={18} />
        </button>
        <div className="flex-1">
          <h3 className="text-lg font-bold">{currentGroup.name || 'Group Chat'}</h3>
          <div className="flex items-center text-sm text-gray-500">
            <Users size={14} className="mr-1" />
            <span>{currentGroup.memberCount || 0} members</span>
          </div>
        </div>
        {isAdmin && (
          <button 
            className="btn btn-sm btn-outline btn-error"
            onClick={handleDeleteGroup}
          >
            <Trash2 size={16} className="mr-1" /> Delete Group
          </button>
        )}
      </div>
      
      <div className="flex-1 overflow-y-auto p-4 space-y-4">
        {isLoading && currentGroupMessages.length === 0 ? (
          <div className="flex justify-center items-center h-full">
            <span className="loading loading-spinner loading-md"></span>
          </div>
        ) : currentGroupMessages.length > 0 ? (
          currentGroupMessages.map((msg) => (
            <div 
              key={msg.id} 
              className={`chat ${msg.sender_id === authUser.id ? 'chat-end' : 'chat-start'}`}
            >
              <div className="chat-header">
                {msg.senderName || `User ${msg.sender_id}`}
                <time className="text-xs opacity-50 ml-1">
                  {formatMessageTime(msg.created_at)}
                </time>
              </div>
              <div className={`chat-bubble ${msg.sender_id === authUser.id ? 'chat-bubble-primary' : ''}`}>
                {msg.text}
              </div>
            </div>
          ))
        ) : (
          <div className="flex justify-center items-center h-full text-gray-500">
            No messages yet. Start the conversation!
          </div>
        )}
        <div ref={messagesEndRef} />
      </div>
      
      <div className="p-4 border-t">
        <form onSubmit={handleSendMessage} className="flex gap-2">
          <input
            type="text"
            className="input input-bordered flex-1"
            placeholder="Type a message..."
            value={message}
            onChange={(e) => setMessage(e.target.value)}
          />
          <button 
            type="submit" 
            className="btn btn-primary"
            disabled={!message.trim()}
          >
            <Send size={18} />
          </button>
        </form>
      </div>
    </div>
  );
};

export default GroupChat;