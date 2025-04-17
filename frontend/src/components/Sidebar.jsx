import { useEffect, useState } from 'react';
import { useChatStore } from '../store/useChatStore';
import { useAuthStore } from '../store/useAuthStore';
import SidebarSkeleton from './skeletons/SidebarSkeleton';
import { Users, Users as GroupIcon } from 'lucide-react';
import { Link, useNavigate } from 'react-router-dom';

const Sidebar = () => {
  const { getUsers, users, selectedUser, setSelectedUser, isUsersLoading } = useChatStore();
  const { onlineUsers } = useAuthStore();
  const [showOnlineOnly, setShowOnlineOnly] = useState(false);
  const navigate = useNavigate();

  useEffect(() => {
    getUsers();
  }, [getUsers]);

  console.log('Sidebar - Users:', users);
  console.log('Sidebar - Online Users:', onlineUsers);

  const filteredUsers = showOnlineOnly
    ? users.filter((user) => onlineUsers.includes(String(user.id)))
    : users;

  const handleUserClick = (user) => {
    setSelectedUser(user);
    navigate('/chat');
  };

  if (isUsersLoading) return <SidebarSkeleton />;

  return (
    <aside className="h-full w-20 lg:w-72 border-r border-base-300 flex flex-col transition-all duration-200">
      <div className="border-b border-base-300 w-full p-5">
        <div className="flex items-center gap-2">
          <Users className="size-6" />
          <span className="font-medium hidden lg:block">Contacts</span>
        </div>
        <div className="mt-3 hidden lg:flex items-center gap-2">
          <label className="cursor-pointer flex items-center gap-2">
            <input
              type="checkbox"
              checked={showOnlineOnly}
              onChange={(e) => setShowOnlineOnly(e.target.checked)}
              className="checkbox checkbox-sm"
            />
            <span className="text-sm">Show online only</span>
          </label>
          <span className="text-xs text-zinc-500">({onlineUsers.length} online)</span>
        </div>
      </div>

      <div className="overflow-y-auto w-full py-3">
        {filteredUsers.map((user) => (
          <button
            key={user.id}
            onClick={() => handleUserClick(user)}
            className={`
              w-full p-3 flex items-center gap-3
              hover:bg-base-300 transition-colors
              ${selectedUser?.id === user.id ? 'bg-base-300 ring-1 ring-base-300' : ''}
            `}
          >
            <div className="relative mx-auto lg:mx-0">
              <img
                src={user.profilePic || '/avatar.png'}
                alt={user.fullName || 'User'}
                className="size-12 object-cover rounded-full"
              />
              {onlineUsers.includes(String(user.id)) && (
                <span
                  className="absolute bottom-0 right-0 size-3 bg-green-500 
                  rounded-full ring-2 ring-zinc-900"
                />
              )}
            </div>

            <div className="hidden lg:block text-left min-w-0">
              <div className="font-medium truncate">
                {user.fullName || 'Unknown User'}
              </div>
              <div className="text-sm text-zinc-400">
                {onlineUsers.includes(String(user.id)) ? 'Online' : 'Offline'}
              </div>
            </div>
          </button>
        ))}

        {filteredUsers.length === 0 && (
          <div className="text-center text-zinc-500 py-4">
            {showOnlineOnly ? 'No online users' : 'No users available'}
          </div>
        )}
      </div>

      <Link to="/groups" className="p-3 hover:bg-base-300 transition-colors">
        <div className="flex items-center gap-3">
          <GroupIcon className="size-6" />
          <span className="font-medium hidden lg:block">Groups</span>
        </div>
      </Link>
    </aside>
  );
};

export default Sidebar;
