import React, { useEffect, useState } from 'react';
import { useGroupStore } from '../store/useGroupStore';
import { useAuthStore } from '../store/useAuthStore';
import { Users, Plus, Search, Loader, UserPlus, MessageSquare, LogOut, Trash2 } from 'lucide-react';
import GroupChat from './GroupChat';
import toast from 'react-hot-toast';

const GroupManagement = ({ userId }) => {
  const { 
    getUserGroups, 
    getAllGroups,
    joinGroup,
    leaveGroup,
    deleteGroup,
    userGroups,
    availableGroups,
    isLoading,
    setCurrentUserId,
    initializeSocket
  } = useGroupStore();
  
  const { authUser } = useAuthStore();
  const [name, setName] = useState('');
  const [searchQuery, setSearchQuery] = useState('');
  const [activeTab, setActiveTab] = useState('myGroups');
  const [selectedGroupId, setSelectedGroupId] = useState(null);

  useEffect(() => {
    const fetchGroups = async () => {
      if (userId) {
        setCurrentUserId(userId);
        initializeSocket(userId);
        await getUserGroups();
        await getAllGroups();
      }
    };

    fetchGroups();
  }, [userId, getUserGroups, getAllGroups, setCurrentUserId, initializeSocket]);

  const handleCreateGroup = async (e) => {
    e.preventDefault();
    try {
      await useGroupStore.getState().createGroup(name);
      toast.success('Group created successfully!');
      setName('');
      await getUserGroups();
      await getAllGroups();
    } catch (error) {
      toast.error(error.response?.data?.error || 'Error creating group');
    }
  };

  const handleJoinGroup = async (groupId) => {
    try {
      await joinGroup(groupId);
      toast.success('Joined group successfully!');
      await getUserGroups();
      await getAllGroups();
    } catch (error) {
      toast.error(error.response?.data?.error || 'Error joining group');
    }
  };

  const handleLeaveGroup = async (groupId) => {
    try {
      await leaveGroup(groupId);
      toast.success('Left group successfully!');
      await getUserGroups();
      await getAllGroups();
    } catch (error) {
      toast.error(error.response?.data?.error || 'Error leaving group');
    }
  };
  
  const handleDeleteGroup = async (groupId) => {
    if (window.confirm('Are you sure you want to delete this group? This action cannot be undone.')) {
      try {
        await deleteGroup(groupId);
        toast.success('Group deleted successfully!');
        await getUserGroups();
        await getAllGroups();
      } catch (error) {
        toast.error(error.response?.data?.error || 'Error deleting group');
      }
    }
  };

  const openGroupChat = (groupId) => {
    setSelectedGroupId(groupId);
  };

  const closeGroupChat = () => {
    setSelectedGroupId(null);
  };

  const filteredUserGroups = Array.isArray(userGroups) ? userGroups.filter(group => 
    group.name.toLowerCase().includes(searchQuery.toLowerCase())
  ) : [];

  const filteredAvailableGroups = Array.isArray(availableGroups) ? availableGroups.filter(group => 
    group.name.toLowerCase().includes(searchQuery.toLowerCase())
  ) : [];

  const userGroupIds = new Set(filteredUserGroups.map(group => group.id));
  const groupsToJoin = filteredAvailableGroups.filter(group => !userGroupIds.has(group.id));

  if (selectedGroupId) {
    return (
      <div className="flex-1 w-full">
        <GroupChat groupId={selectedGroupId} userId={userId} onBack={closeGroupChat} />
      </div>
    );
  }

  return (
    <div className="flex-1 flex flex-col overflow-auto w-full">
      <div className="bg-base-200 p-4 flex items-center justify-between border-b">
        <div className="flex items-center space-x-2">
          <Users className="text-primary" />
          <h2 className="text-xl font-bold">Group Chat</h2>
        </div>
        <div className="tabs tabs-boxed">
          <a 
            className={`tab ${activeTab === 'myGroups' ? 'tab-active' : ''}`}
            onClick={() => setActiveTab('myGroups')}
          >
            My Groups
          </a>
          <a 
            className={`tab ${activeTab === 'discover' ? 'tab-active' : ''}`}
            onClick={() => setActiveTab('discover')}
          >
            Discover
          </a>
        </div>
      </div>

      <div className="flex-1 overflow-y-auto p-4 space-y-4">
        <div className="card bg-base-100 shadow-md">
          <div className="card-body">
            <h3 className="card-title text-lg flex items-center">
              <Plus size={18} className="mr-2" />
              Create New Group
            </h3>
            <form onSubmit={handleCreateGroup} className="flex gap-2">
              <input
                type="text"
                placeholder="Enter group name"
                className="input input-bordered flex-1"
                value={name}
                onChange={(e) => setName(e.target.value)}
                required
              />
              <button 
                type="submit" 
                className="btn btn-primary"
                disabled={isLoading}
              >
                {isLoading ? (
                  <>
                    <Loader className="animate-spin mr-2" size={16} />
                    Creating...
                  </>
                ) : (
                  'Create'
                )}
              </button>
            </form>
          </div>
        </div>

        <div className="w-full">
          <div className="relative">
            <input
              type="text"
              placeholder="Search groups..."
              className="input input-bordered w-full pr-10"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
            />
            <Search size={18} className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
          </div>
        </div>

        {activeTab === 'myGroups' && (
          <div className="card bg-base-100 shadow-md">
            <div className="card-body">
              <h3 className="card-title text-lg">My Groups</h3>
              {isLoading ? (
                <div className="flex justify-center py-8">
                  <Loader className="animate-spin" />
                </div>
              ) : filteredUserGroups.length > 0 ? (
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  {filteredUserGroups.map(group => (
                    <div key={group.id} className="card bg-base-200">
                      <div className="card-body">
                        <h4 className="card-title text-md">
                          {group.name}
                          {group.is_admin && <span className="badge badge-primary ml-2">Admin</span>}
                        </h4>
                        <p className="text-sm text-gray-500">
                          {group.memberCount || 0} members
                        </p>
                        <div className="card-actions justify-end mt-2">
                          <button 
                            className="btn btn-sm btn-primary"
                            onClick={() => openGroupChat(group.id)}
                          >
                            <MessageSquare size={16} className="mr-1" /> Chat
                          </button>
                          
                          {group.is_admin && (
                            <button 
                              className="btn btn-sm btn-outline btn-error"
                              onClick={() => handleDeleteGroup(group.id)}
                            >
                              <Trash2 size={16} className="mr-1" /> Delete
                            </button>
                          )}
                          
                          {!group.is_admin && (
                            <button 
                              className="btn btn-sm btn-outline btn-error"
                              onClick={() => handleLeaveGroup(group.id)}
                            >
                              <LogOut size={16} className="mr-1" /> Leave
                            </button>
                          )}
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="text-center py-8 text-gray-500">
                  {searchQuery ? 'No groups match your search' : 'You haven\'t joined any groups yet. Discover groups to join!'}
                </div>
              )}
            </div>
          </div>
        )}

        {activeTab === 'discover' && (
          <div className="card bg-base-100 shadow-md">
            <div className="card-body">
              <h3 className="card-title text-lg">Discover Groups</h3>
              {isLoading ? (
                <div className="flex justify-center py-8">
                  <Loader className="animate-spin" />
                </div>
              ) : groupsToJoin.length > 0 ? (
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  {groupsToJoin.map(group => (
                    <div key={group.id} className="card bg-base-200">
                      <div className="card-body">
                        <h4 className="card-title text-md">{group.name}</h4>
                        <p className="text-sm text-gray-500">
                          {group.memberCount || 0} members
                        </p>
                        <div className="card-actions justify-end mt-2">
                          <button 
                            className="btn btn-sm btn-primary"
                            onClick={() => handleJoinGroup(group.id)}
                          >
                            <UserPlus size={16} className="mr-1" /> Join
                          </button>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="text-center py-8 text-gray-500">
                  {searchQuery ? 'No groups match your search' : 'No new groups to join. Create one to get started!'}
                </div>
              )}
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default GroupManagement;