@extends('components.layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-white">User Management</h1>
        <button 
            onclick="openCreateUserModal()"
            class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors"
        >
            Add New User
        </button>
    </div>

    {{-- User Filters --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 flex justify-between items-center">
        <div class="flex space-x-4">
            <select name="status" class="bg-gray-800 text-white px-4 py-2 rounded-lg" onchange="this.form.submit()">
                <option value="">All Users</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Users</option>
                <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned Users</option>
            </select>
            <select name="sort" class="bg-gray-800 text-white px-4 py-2 rounded-lg" onchange="this.form.submit()">
                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Sort by Joined Date</option>
                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Sort by Username</option>
            </select>
        </div>
        <div class="relative">
            <input 
                type="text" 
                name="search"
                value="{{ request('search') }}"
                placeholder="Search users..." 
                class="bg-gray-800 text-white px-4 py-2 pl-10 rounded-lg w-64"
            >
            <i class="ri-search-line absolute left-3 top-3 text-gray-400"></i>
        </div>
    </form>

    {{-- Users Table --}}
    <div class="bg-gray-800/50 backdrop-blur-md rounded-lg overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-700/50">
                <tr>
                    <th class="p-4">Avatar</th>
                    <th class="p-4">Username</th>
                    <th class="p-4">Email</th>
                    <th class="p-4">Joined Date</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="border-b border-gray-700/50 hover:bg-gray-700/20 transition-colors">
                    <td class="p-4">
                        <img 
                            src="{{ $user->avatar_url ?? '/default-avatar.png' }}" 
                            alt="{{ $user->name }}" 
                            class="w-12 h-12 rounded-full object-cover"
                        >
                    </td>
                    <td class="p-4">{{ $user->name }}</td>
                    <td class="p-4">{{ $user->email }}</td>
                    <td class="p-4">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="p-4">
                        <span class="{{ $user->is_banned ? 'text-red-500' : 'text-green-500' }}">
                            {{ $user->is_banned ? 'Banned' : 'Active' }}
                        </span>
                    </td>
                    <td class="p-4">
                        <div class="flex space-x-2">
                            <button 
                                onclick="viewUserDetails({{ $user->id }})"
                                class="text-blue-500 hover:text-blue-400"
                                title="View Details"
                            >
                                <i class="ri-eye-line"></i>
                            </button>
                            <form 
                                action="{{ route('admin.users.toggle-ban', $user->id) }}" 
                                method="POST" 
                                class="inline"
                            >
                                @csrf
                                @method('PATCH')
                                <button 
                                    type="submit"
                                    class="{{ $user->is_banned ? 'text-green-500 hover:text-green-400' : 'text-red-500 hover:text-red-400' }}"
                                    title="{{ $user->is_banned ? 'Unban User' : 'Ban User' }}"
                                >
                                    <i class="ri-{{ $user->is_banned ? 'play-line' : 'stop-line' }}"></i>
                                </button>
                            </form>
                            <button 
                                onclick="confirmDeleteUser({{ $user->id }})"
                                class="text-red-500 hover:text-red-400"
                                title="Delete User"
                            >
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        {{ $users->links() }}
    </div>

    {{-- Create User Modal --}}
    <div id="createUserModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-gray-800 rounded-lg w-full max-w-2xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">Add New User</h2>
                <button 
                    onclick="closeCreateUserModal()"
                    class="text-gray-400 hover:text-white"
                >
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>

            <form 
                action="{{ route('admin.users.store') }}" 
                method="POST" 
                enctype="multipart/form-data"
            >
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm text-gray-400 mb-2">Username</label>
                        <input 
                            type="text" 
                            name="username" 
                            required 
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm text-gray-400 mb-2">Email</label>
                        <input 
                            type="email" 
                            name="email" 
                            required 
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm text-gray-400 mb-2">Password</label>
                        <input 
                            type="password" 
                            name="password" 
                            required 
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm text-gray-400 mb-2">Confirm Password</label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm text-gray-400 mb-2">Profile Picture</label>
                        <input 
                            type="file" 
                            name="avatar" 
                            accept="image/*" 
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary file:mr-4 file:rounded-full file:border-0 file:bg-primary file:text-white file:px-4 file:py-2"
                        >
                    </div>

                    <div class="col-span-2">
                        <button 
                            type="submit" 
                            class="w-full bg-primary text-white py-3 rounded-lg hover:bg-primary-dark transition-colors"
                        >
                            Create User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- User Details Modal --}}
    <div id="userDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-gray-800 rounded-lg w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">User Details</h2>
                <button 
                    onclick="closeUserDetailsModal()"
                    class="text-gray-400 hover:text-white"
                >
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>

            <div id="userDetailsContent" class="text-center">
                <!-- User details will be dynamically populated here -->
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteUserModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-gray-800 rounded-lg w-full max-w-md p-6">
            <h2 class="text-2xl font-bold text-white mb-4">Confirm Deletion</h2>
            <p class="text-gray-400 mb-6">Are you sure you want to delete this user?</p>
            <div class="flex justify-end space-x-4">
                <button 
                    onclick="closeDeleteUserModal()"
                    class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600"
                >
                    Cancel
                </button>
                <button 
                    id="confirmDeleteButton"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600"
                >
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openCreateUserModal() {
        document.getElementById('createUserModal').classList.remove('hidden');
    }

    function closeCreateUserModal() {
        document.getElementById('createUserModal').classList.add('hidden');
    }

    function viewUserDetails(userId) {
        fetch(`/admin/users/${userId}/details`)
            .then(response => response.json())
            .then(data => {
                const detailsContent = document.getElementById('userDetailsContent');
                detailsContent.innerHTML = `
                    <img 
                        src="${data.avatar_url}" 
                        alt="User Avatar" 
                        class="w-24 h-24 rounded-full object-cover mx-auto mb-4"
                    >
                    <h3 class="text-xl font-semibold text-white">${data.username}</h3>
                    <p class="text-gray-400 mb-4">${data.email}</p>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Joined Date</span>
                            <span>${data.created_at}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Total Watchlist</span>
                            <span>${data.watchlist_count}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Favorite Genres</span>
                            <span>${data.favorite_genres ? data.favorite_genres.join(', ') : 'N/A'}</span>
                        </div>
                    </div>
                `;
                document.getElementById('userDetailsModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error fetching user details:', error);
            });
    }

    function closeUserDetailsModal() {
        document.getElementById('userDetailsModal').classList.add('hidden');
    }

    function confirmDeleteUser(userId) {
        const deleteModal = document.getElementById('deleteUserModal');
        deleteModal.classList.remove('hidden');
        
        const confirmDeleteButton = document.getElementById('confirmDeleteButton');
        confirmDeleteButton.onclick = function() {
            fetch(`/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(() => {
                window.location.reload();
            })
            .catch(error => {
                console.error('Error deleting user:', error);
            });
        };
    }

    function closeDeleteUserModal() {
        document.getElementById('deleteUserModal').classList.add('hidden');
    }

    document.addEventListener('click', function(event) {
        const modals = [
            document.getElementById('createUserModal'),
            document.getElementById('userDetailsModal'),
            document.getElementById('deleteUserModal')
        ];

        modals.forEach(modal => {
            if (modal && event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.getElementById('createUserModal').classList.add('hidden');
            document.getElementById('userDetailsModal').classList.add('hidden');
            document.getElementById('deleteUserModal').classList.add('hidden');
        }
    });
</script>
@endpush