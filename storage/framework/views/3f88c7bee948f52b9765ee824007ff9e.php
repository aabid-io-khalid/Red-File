

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-white">User Management</h1>
        <button 
            onclick="openCreateUserModal()"
            class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors flex items-center gap-2"
        >
            <i class="ri-user-add-line"></i> Add New User
        </button>
    </div>

    
    <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" class="mb-6 flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4">
        <div class="flex flex-wrap gap-4">
            <select name="status" class="bg-gray-800 text-white px-4 py-2 rounded-lg border border-gray-700 focus:ring-2 focus:ring-primary focus:outline-none" onchange="this.form.submit()">
                <option value="">All Users</option>
                <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Active Users</option>
                <option value="banned" <?php echo e(request('status') == 'banned' ? 'selected' : ''); ?>>Banned Users</option>
            </select>
            <select name="sort" class="bg-gray-800 text-white px-4 py-2 rounded-lg border border-gray-700 focus:ring-2 focus:ring-primary focus:outline-none" onchange="this.form.submit()">
                <option value="created_at" <?php echo e(request('sort') == 'created_at' ? 'selected' : ''); ?>>Sort by Joined Date</option>
                <option value="name" <?php echo e(request('sort') == 'name' ? 'selected' : ''); ?>>Sort by Username</option>
            </select>
        </div>
        <div class="relative">
            <input 
                type="text" 
                name="search"
                value="<?php echo e(request('search')); ?>"
                placeholder="Search users..." 
                class="bg-gray-800 text-white px-4 py-2 pl-10 rounded-lg w-full md:w-64 border border-gray-700 focus:ring-2 focus:ring-primary focus:outline-none"
            >
            <i class="ri-search-line absolute left-3 top-3 text-gray-400"></i>
        </div>
    </form>

    
    <div class="bg-gray-800/50 backdrop-blur-md rounded-lg overflow-x-auto shadow-lg">
        <table class="w-full text-left">
            <thead class="bg-gray-700/80">
                <tr>
                    <th class="p-4">Username</th>
                    <th class="p-4">Email</th>
                    <th class="p-4">Joined Date</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-b border-gray-700/50 hover:bg-gray-700/30 transition-colors">
                    <td class="p-4 font-medium"><?php echo e($user->name); ?></td>
                    <td class="p-4"><?php echo e($user->email); ?></td>
                    <td class="p-4"><?php echo e($user->created_at->format('M d, Y')); ?></td>
                    <td class="p-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo e($user->is_banned ? 'bg-red-500/20 text-red-400' : 'bg-green-500/20 text-green-400'); ?>" id="status-badge-<?php echo e($user->id); ?>">
                            <?php echo e($user->is_banned ? 'Banned' : 'Active'); ?>

                        </span>
                    </td>
                    <td class="p-4 text-center">
                        <button 
                            onclick="toggleUserBan(<?php echo e($user->id); ?>, <?php echo e($user->is_banned ? 'true' : 'false'); ?>)"
                            class="<?php echo e($user->is_banned ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'); ?> text-white px-3 py-1 rounded-lg transition-colors flex items-center gap-1 mx-auto"
                            id="ban-button-<?php echo e($user->id); ?>"
                        >
                            <i class="ri-<?php echo e($user->is_banned ? 'user-unfollow-line' : 'user-forbid-line'); ?>" id="ban-icon-<?php echo e($user->id); ?>"></i>
                            <span id="ban-text-<?php echo e($user->id); ?>"><?php echo e($user->is_banned ? 'Unban' : 'Ban'); ?></span>
                        </button>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        
        <div class="p-4">
            <?php echo e($users->links()); ?>

        </div>
    </div>

    
    <div id="createUserModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 hidden">
        <div class="bg-gray-800 rounded-lg w-full max-w-lg p-6 shadow-2xl border border-gray-700">
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
                action="<?php echo e(route('admin.users.store')); ?>" 
                method="POST"
            >
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm text-gray-400 mb-2">Username</label>
                        <input 
                            type="text" 
                            name="username" 
                            required 
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary border border-gray-600"
                        >
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm text-gray-400 mb-2">Email</label>
                        <input 
                            type="email" 
                            name="email" 
                            required 
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary border border-gray-600"
                        >
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm text-gray-400 mb-2">Password</label>
                        <input 
                            type="password" 
                            name="password" 
                            required 
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary border border-gray-600"
                        >
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm text-gray-400 mb-2">Confirm Password</label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary border border-gray-600"
                        >
                    </div>

                    <div class="col-span-2 mt-4">
                        <button 
                            type="submit" 
                            class="w-full bg-primary text-white py-3 rounded-lg hover:bg-primary-dark transition-colors flex items-center justify-center gap-2"
                        >
                            <i class="ri-user-add-line"></i> Create User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Add CSRF token to all AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    function openCreateUserModal() {
        document.getElementById('createUserModal').classList.remove('hidden');
    }

    function closeCreateUserModal() {
        document.getElementById('createUserModal').classList.add('hidden');
    }
    
    function toggleUserBan(userId, isBanned) {
        // Optimistically update the UI immediately
        const statusBadge = document.getElementById(`status-badge-${userId}`);
        const banButton = document.getElementById(`ban-button-${userId}`);
        const banIcon = document.getElementById(`ban-icon-${userId}`);
        const banText = document.getElementById(`ban-text-${userId}`);
        
        if (isBanned) {
            // User is currently banned, so we're unbanning
            statusBadge.className = 'px-2 py-1 rounded-full text-xs font-medium bg-green-500/20 text-green-400';
            statusBadge.textContent = 'Active';
            banButton.className = 'bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg transition-colors flex items-center gap-1 mx-auto';
            banIcon.className = 'ri-user-forbid-line';
            banText.textContent = 'Ban';
        } else {
            // User is currently active, so we're banning
            statusBadge.className = 'px-2 py-1 rounded-full text-xs font-medium bg-red-500/20 text-red-400';
            statusBadge.textContent = 'Banned';
            banButton.className = 'bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg transition-colors flex items-center gap-1 mx-auto';
            banIcon.className = 'ri-user-unfollow-line';
            banText.textContent = 'Unban';
        }
        
        // Send AJAX request to update the server
        fetch(`/admin/users/${userId}/toggle-ban`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Success notification could be added here
            console.log('User ban status updated:', data);
        })
        .catch(error => {
            // Revert UI changes on error
            if (isBanned) {
                statusBadge.className = 'px-2 py-1 rounded-full text-xs font-medium bg-red-500/20 text-red-400';
                statusBadge.textContent = 'Banned';
                banButton.className = 'bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg transition-colors flex items-center gap-1 mx-auto';
                banIcon.className = 'ri-user-unfollow-line';
                banText.textContent = 'Unban';
            } else {
                statusBadge.className = 'px-2 py-1 rounded-full text-xs font-medium bg-green-500/20 text-green-400';
                statusBadge.textContent = 'Active';
                banButton.className = 'bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg transition-colors flex items-center gap-1 mx-auto';
                banIcon.className = 'ri-user-forbid-line';
                banText.textContent = 'Ban';
            }
            console.error('Error updating user ban status:', error);
        });
    }

    document.addEventListener('click', function(event) {
        const modal = document.getElementById('createUserModal');
        if (modal && event.target === modal) {
            modal.classList.add('hidden');
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.getElementById('createUserModal').classList.add('hidden');
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('components.layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Youcode\Herd\file-rouge\resources\views/admin/user.blade.php ENDPATH**/ ?>