{{-- users/index.blade.php --}}
@extends('admin.layout')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-white">User Management</h1>
        <button 
            x-data 
            @click="$dispatch('open-modal', 'create-user-modal')"
            class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors"
        >
            Add New User
        </button>
    </div>

    {{-- User Filters --}}
    <div class="mb-6 flex justify-between items-center">
        <div class="flex space-x-4">
            <select class="bg-gray-800 text-white px-4 py-2 rounded-lg">
                <option>All Users</option>
                <option>Active Users</option>
                <option>Banned Users</option>
                <option>Admins</option>
            </select>
            <select class="bg-gray-800 text-white px-4 py-2 rounded-lg">
                <option>Sort by Joined Date</option>
                <option>Sort by Username</option>
                <option>Sort by Last Active</option>
            </select>
        </div>
        <div class="relative">
            <input 
                type="text" 
                placeholder="Search users..." 
                class="bg-gray-800 text-white px-4 py-2 pl-10 rounded-lg w-64"
            >
            <i class="ri-search-line absolute left-3 top-3 text-gray-400"></i>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="bg-gray-800/50 backdrop-blur-md rounded-lg overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-700/50">
                <tr>
                    <th class="p-4">Avatar</th>
                    <th class="p-4">Username</th>
                    <th class="p-4">Email</th>
                    <th class="p-4">Joined Date</th>
                    <th class="p-4">Role</th>
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
                            alt="{{ $user->username }}" 
                            class="w-12 h-12 rounded-full object-cover"
                        >
                    </td>
                    <td class="p-4">{{ $user->username }}</td>
                    <td class="p-4">{{ $user->email }}</td>
                    <td class="p-4">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="p-4">
                        <span class="
                            px-2 py-1 rounded-full text-xs font-medium
                            {{ $user->role === 'admin' ? 'bg-primary text-white' : 'bg-gray-700 text-gray-300' }}
                        ">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="p-4">
                        <span class="{{ $user->is_banned ? 'text-red-500' : 'text-green-500' }}">
                            {{ $user->is_banned ? 'Banned' : 'Active' }}
                        </span>
                    </td>
                    <td class="p-4">
                        <div class="flex space-x-2">
                            <button 
                                @click="viewUserDetails({{ $user->id }})"
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
                                @click="deleteUser({{ $user->id }})"
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
        <div class="p-4">
            {{ $users->links('admin.components.pagination') }}
        </div>
    </div>

    {{-- Create User Modal --}}
    <div 
        x-data="{ isOpen: false }"
        x-show="isOpen"
        @open-modal.window="if ($event.detail === 'create-user-modal') isOpen = true"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        x-cloak
    >
        <div class="bg-gray-800 rounded-lg w-full max-w-2xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">Add New User</h2>
                <button 
                    @click="isOpen = false"
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
                        <label class="block text-sm text-gray-400 mb-2">Role</label>
                        <select 
                            name="role" 
                            required 
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
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
    <div 
        x-data="{ isDetailsOpen: false, userDetails: null }"
        x-show="isDetailsOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        x-cloak
    >
        <div class="bg-gray-800 rounded-lg w-full max-w-md p-6" x-show="userDetails">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">User Details</h2>
                <button 
                    @click="isDetailsOpen = false"
                    class="text-gray-400 hover:text-white"
                >
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>

            <div class="text-center mb-6">
                <img 
                    :src="userDetails?.avatar_url" 
                    alt="User Avatar" 
                    class="w-24 h-24 rounded-full object-cover mx-auto mb-4"
                >
                <h3 class="text-xl font-semibold text-white" x-text="userDetails?.username"></h3>
                <p class="text-gray-400" x-text="userDetails?.email"></p>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-400">Joined Date</span>
                    <span x-text="userDetails?.created_at"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Role</span>
                    <span x-text="userDetails?.role"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Total Watchlist</span>
                    <span x-text="userDetails?.watchlist_count"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Favorite Genres</span>
                    <span x-text="userDetails?.favorite_genres?.join(', ')"></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function viewUserDetails(id) {
        fetch(`/admin/users/${id}/details`)
            .then(response => response.json())
            .then(data => {
                let detailsModal = document.querySelector('[x-data="{ isDetailsOpen: false, userDetails: null }"]');
                Alpine.data('userDetails', data);
                Alpine.data('isDetailsOpen', true);
            })
            .catch(error => {
                console.error('Error fetching user details:', error);
            });
    }

    function deleteUser(id) {
        if (confirm('Are you sure you want to delete this user?')) {
            fetch(`/admin/users/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                window.location.reload();
            })
            .catch(error => {
                console.error('Error deleting user:', error);
            });
        }
    }
</script>
@endpush