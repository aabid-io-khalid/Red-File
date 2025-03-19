@extends('components.layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="{ isOpen: false }">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-white">User  Management</h1>
        <button 
            @click="isOpen = true"
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
                {{-- Hardcoded User 1 --}}
                <tr class="border-b border-gray-700/50 hover:bg-gray-700/20 transition-colors">
                    <td class="p-4">
                        <img 
                            src="/path/to/avatar1.png" 
                            alt="User  1" 
                            class="w-12 h-12 rounded-full object-cover"
                        >
                    </td>
                    <td class="p-4">user1</td>
                    <td class="p-4">user1@example.com</td>
                    <td class="p-4">Jan 01, 2023</td>
                    <td class="p-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-primary text-white">
                            Admin
                        </span>
                    </td>
                    <td class="p-4">
                        <span class="text-green-500">Active</span>
                    </td>
                    <td class="p-4">
                        <div class="flex space-x-2">
                            <button 
                                class="text-blue-500 hover:text-blue-400"
                                title="View Details"
                            >
                                <i class="ri-eye-line"></i>
                            </button>
                            <button 
                                class="text-red-500 hover:text-red-400"
                                title="Ban User"
                            >
                                <i class="ri-stop-line"></i>
                            </button>
                            <button 
                                class="text-red-500 hover:text-red-400"
                                title="Delete User"
                            >
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                {{-- Hardcoded User 2 --}}
                <tr class="border-b border-gray-700/50 hover:bg-gray-700/20 transition-colors">
                    <td class="p-4">
                        <img 
                            src="/path/to/avatar2.png" 
                            alt="User  2" 
                            class="w-12 h-12 rounded-full object-cover"
                        >
                    </td>
                    <td class="p-4">user2</td>
                    <td class="p-4">user2@example.com</td>
                    <td class="p-4">Feb 15, 2023</td>
                    <td class="p-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-700 text-gray-300">
                            User
                        </span>
                    </td>
                    <td class="p-4">
                        <span class="text-red-500">Banned</span>
                    </td>
                    <td class="p-4">
                        <div class="flex space-x-2">
                            <button 
                                class="text-blue-500 hover:text-blue-400"
                                title="View Details"
                            >
                                <i class="ri-eye-line"></i>
                            </button>
                            <button 
                                class="text-green-500 hover:text-green-400"
                                title="Unban User"
                            >
                                <i class="ri-play-line"></i>
                            </button>
                            <button 
                                class="text-red-500 hover:text-red-400"
                                title="Delete User"
                            >
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Pagination (Static for this example) --}}
        <div class="p-4">
            <span class="text-gray-400">Showing 1 to 2 of 2 users</span>
        </div>
    </div>

    {{-- Create User Modal --}}
    <div 
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
                action="#" 
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
                            <option value="user">User </option>
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
</div>
@endsection

@push('scripts')
<script>
    
</script>
@endpush