

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-white">Movies Management</h1>
        <button 
            type="button" 
            class="bg-primary hover:bg-primary/90 px-4 py-2 rounded text-white flex items-center"
            onclick="openMovieModal('add')"
        >
            <i class="ri-add-line mr-1"></i> Add New Movie
        </button>
    </div>

    <!-- Search Bar -->
    <div class="mb-6">
        <div class="relative">
            <input 
                type="text" 
                id="movie-search" 
                placeholder="Search movies by title..." 
                class="bg-gray-800 text-white w-full px-4 py-2 rounded-lg border border-gray-700 focus:outline-none focus:border-primary pl-10"
            >
            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>
        <div id="search-no-results" class="hidden text-center text-gray-400 mt-4">
            No movies found matching your search.
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if($hasDbError): ?>
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
            <strong>Warning:</strong> Could not connect to database. Some features may be limited. *Guess the server’s hungover again.*
        </div>
    <?php endif; ?>

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-700">
        <ul class="flex flex-wrap -mb-px">
            <li class="mr-2">
                <button class="inline-block p-4 border-b-2 <?php echo e(request('tab') != 'api' ? 'border-primary text-primary' : 'border-transparent hover:text-gray-300'); ?> rounded-t-lg" 
                    id="local-tab" onclick="switchTab('local')">
                    Local Movies
                </button>
            </li>
            <li class="mr-2">
                <button class="inline-block p-4 border-b-2 <?php echo e(request('tab') == 'api' ? 'border-primary text-primary' : 'border-transparent hover:text-gray-300'); ?> rounded-t-lg" 
                    id="api-tab" onclick="switchTab('api')">
                    TMDB API Movies
                </button>
            </li>
        </ul>
    </div>

    <!-- Local Movies Tab -->
    <div id="local-content" class="tab-content <?php echo e(request('tab') == 'api' ? 'hidden' : ''); ?>">
        <?php if($hasDbError): ?>
            <div class="text-center py-6">
                <p class="text-gray-400">Database connection is unavailable. Cannot display local movies. *It’s probably plotting its escape.*</p>
            </div>
        <?php elseif(isset($localMovies) && count($localMovies) === 0): ?>
            <div class="text-center py-6">
                <p class="text-gray-400">No movies created yet. Add your first movie!</p>
            </div>
        <?php elseif(isset($localMovies) && count($localMovies) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php $__currentLoopData = $localMovies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-dark rounded-lg overflow-hidden border border-gray-800 movie-card relative" data-title="<?php echo e(strtolower($movie->title)); ?>">
                    <div class="relative">
                        <img 
                            src="<?php echo e($movie->poster ?? '/img/no-poster.jpg'); ?>" 
                            alt="<?php echo e($movie->title); ?>"
                            class="w-full h-64 object-cover card-image transition-all duration-300"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-70"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-bold"><?php echo e($movie->title); ?></h3>
                            <div class="flex items-center mt-1">
                                <span class="text-yellow-500 mr-1"><i class="ri-star-fill"></i></span>
                                <span class="text-white text-sm"><?php echo e($movie->rating ?? 'N/A'); ?></span>
                                <span class="text-gray-400 text-sm ml-2"><?php echo e($movie->year ?? 'N/A'); ?></span>
                            </div>
                        </div>
                        <?php if($movie->is_banned): ?>
                        <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                            BANNED
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <p class="text-gray-300 text-sm line-clamp-3 h-18"><?php echo e($movie->description ?? 'No description available.'); ?></p>
                        </div>
                        <div class="mb-2">
                            <span class="text-gray-400 text-xs">Categories:</span>
                            <span class="text-gray-300 text-xs"><?php echo e($movie->categories->pluck('name')->implode(', ')); ?></span>
                        </div>
                        <div class="mb-2">
                            <span class="text-gray-400 text-xs">Created:</span>
                            <span class="text-gray-300 text-xs"><?php echo e($movie->created_at->format('M d, Y')); ?></span>
                        </div>
                        <div class="mt-4 flex justify-between items-center">
                            <div>
                                <button 
                                    class="px-3 py-1 rounded text-sm <?php echo e($movie->is_banned ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'); ?> text-white ban-button"
                                    data-id="<?php echo e($movie->id); ?>"
                                    data-is-tmdb="false"
                                    onclick="toggleBan(<?php echo e($movie->id); ?>, false)"
                                >
                                    <?php echo e($movie->is_banned ? 'Unban' : 'Ban'); ?>

                                </button>
                            </div>
                            <div>
                                <button 
                                    onclick="openMovieModal('edit', <?php echo e($movie->id); ?>)" 
                                    class="text-blue-500 hover:text-blue-700 mr-2"
                                >
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button 
                                    onclick="deleteMovie(<?php echo e($movie->id); ?>)" 
                                    class="text-red-500 hover:text-red-700"
                                >
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="text-center py-6">
                <p class="text-gray-400">No movies available. *Guess they all got lost in the void.*</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- TMDB API Movies Tab -->
    <div id="api-content" class="tab-content <?php echo e(request('tab') == 'api' ? '' : 'hidden'); ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php if(isset($apiMovies) && !empty($apiMovies)): ?>
                <?php $__currentLoopData = $apiMovies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-dark rounded-lg overflow-hidden border border-gray-800 movie-card relative" data-title="<?php echo e(strtolower($movie['title'] ?? 'unknown title')); ?>">
                    <div class="relative">
                        <img 
                            src="<?php echo e(isset($movie['poster_path']) && $movie['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'] : '/img/no-poster.jpg'); ?>" 
                            alt="<?php echo e($movie['title'] ?? 'Movie'); ?>"
                            class="w-full h-64 object-cover card-image transition-all duration-300"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-70"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-bold"><?php echo e($movie['title'] ?? 'Unknown Title'); ?></h3>
                            <div class="flex items-center mt-1">
                                <span class="text-yellow-500 mr-1"><i class="ri-star-fill"></i></span>
                                <span class="text-white text-sm"><?php echo e(isset($movie['vote_average']) ? number_format($movie['vote_average'], 1) : 'N/A'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-300 text-sm line-clamp-3 h-18"><?php echo e($movie['overview'] ?? 'No description available.'); ?></p>
                        <div class="mt-4 flex justify-between items-center">
                            <button 
                                class="px-3 py-1 rounded text-sm <?php echo e(isset($bannedApiMovieIds) && in_array($movie['id'], $bannedApiMovieIds) ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'); ?> text-white ban-button"
                                data-id="<?php echo e($movie['id']); ?>"
                                data-is-tmdb="true"
                                data-tmdb-id="<?php echo e($movie['id']); ?>"
                                onclick="toggleBan(<?php echo e($movie['id']); ?>, true)"
                            >
                                <?php echo e(isset($bannedApiMovieIds) && in_array($movie['id'], $bannedApiMovieIds) ? 'Unban' : 'Ban'); ?>

                            </button>
                        </div>
                    </div>
                    <?php if(isset($bannedApiMovieIds) && in_array($movie['id'], $bannedApiMovieIds)): ?>
                    <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                        BANNED
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <div class="col-span-full text-center py-6">
                <p class="text-gray-400">Unable to fetch movies from TMDB API. Please try again later. *Maybe it’s napping.*</p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination for TMDB API Movies -->
        <?php if(isset($apiMoviesPaginator) && $apiMoviesPaginator->lastPage() > 1): ?>
        <div class="mt-8 flex justify-center">
            <nav class="flex items-center">
                <?php if($apiMoviesPaginator->onFirstPage()): ?>
                    <span class="px-3 py-1 bg-gray-800 text-gray-500 rounded-l cursor-not-allowed">Previous</span>
                <?php else: ?>
                    <a href="<?php echo e($apiMoviesPaginator->previousPageUrl()); ?>" class="px-3 py-1 bg-gray-800 text-primary hover:bg-gray-700 rounded-l">Previous</a>
                <?php endif; ?>
                
                <div class="flex">
                    <?php
                        $start = max($apiMoviesPaginator->currentPage() - 2, 1);
                        $end = min($start + 4, $apiMoviesPaginator->lastPage());
                        if ($end - $start < 4 && $start > 1) {
                            $start = max(1, $end - 4);
                        }
                    ?>
                    
                    <?php if($start > 1): ?>
                        <a href="<?php echo e($apiMoviesPaginator->url(1)); ?>" class="px-3 py-1 bg-gray-800 text-white hover:bg-gray-700">1</a>
                        <?php if($start > 2): ?>
                            <span class="px-2 py-1 bg-gray-800 text-gray-500">...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for($i = $start; $i <= $end; $i++): ?>
                        <a href="<?php echo e($apiMoviesPaginator->url($i)); ?>" 
                           class="px-3 py-1 <?php echo e($apiMoviesPaginator->currentPage() == $i ? 'bg-primary text-white' : 'bg-gray-800 text-white hover:bg-gray-700'); ?>">
                            <?php echo e($i); ?>

                        </a>
                    <?php endfor; ?>
                    
                    <?php if($end < $apiMoviesPaginator->lastPage()): ?>
                        <?php if($end < $apiMoviesPaginator->lastPage() - 1): ?>
                            <span class="px-2 py-1 bg-gray-800 text-gray-500">...</span>
                        <?php endif; ?>
                        <a href="<?php echo e($apiMoviesPaginator->url($apiMoviesPaginator->lastPage())); ?>" class="px-3 py-1 bg-gray-800 text-white hover:bg-gray-700"><?php echo e($apiMoviesPaginator->lastPage()); ?></a>
                    <?php endif; ?>
                </div>
                
                <?php if($apiMoviesPaginator->hasMorePages()): ?>
                    <a href="<?php echo e($apiMoviesPaginator->nextPageUrl()); ?>" class="px-3 py-1 bg-gray-800 text-primary hover:bg-gray-700 rounded-r">Next</a>
                <?php else: ?>
                    <span class="px-3 py-1 bg-gray-800 text-gray-500 rounded-r cursor-not-allowed">Next</span>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>
    </div>

    <!-- Movie Modal (Add/Edit) -->
    <div id="movie-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50 overflow-y-auto">
        <div class="bg-dark rounded-lg w-full max-w-2xl p-6 m-4 relative">
            <button onclick="closeMovieModal()" class="absolute top-4 right-4 text-gray-400 hover:text-white">
                <i class="ri-close-line text-xl"></i>
            </button>
            <h2 id="movie-modal-title" class="text-2xl font-semibold mb-6 text-white flex items-center">
                <i class="ri-film-line mr-2"></i>
                <span>Add New Movie</span>
            </h2>
            <form id="movie-form" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" id="movie-id">
                <input type="hidden" name="_method" id="form-method" value="POST">
                
                <div class="col-span-1">
                    <label for="title" class="block text-sm font-medium text-gray-300 mb-1">Title</label>
                    <input type="text" name="title" id="title" class="bg-gray-800 text-white w-full px-4 py-2 rounded border border-gray-700 focus:outline-none focus:border-primary" required>
                </div>
                
                <div class="col-span-1">
                    <label for="year" class="block text-sm font-medium text-gray-300 mb-1">Year</label>
                    <input type="number" name="year" id="year" min="1900" max="<?php echo e(date('Y')); ?>" class="bg-gray-800 text-white w-full px-4 py-2 rounded border border-gray-700 focus:outline-none focus:border-primary">
                </div>
                
                <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                    <textarea name="description" id="description" rows="4" class="bg-gray-800 text-white w-full px-4 py-2 rounded border border-gray-700 focus:outline-none focus:border-primary"></textarea>
                </div>
                
                <div class=" xemcol-span-1">
                    <label for="rating" class="block text-sm font-medium text-gray-300 mb-1">Rating (0-10)</label>
                    <input type="number" name="rating" id="rating" min="0" max="10" step="0.1" class="bg-gray-800 text-white w-full px-4 py-2 rounded border border-gray-700 focus:outline-none focus:border-primary">
                </div>
                
                <div class="col-span-1">
                    <label for="categories" class="block text-sm font-medium text-gray-300 mb-1">Categories (comma-separated)</label>
                    <input type="text" name="categories" id="categories" class="bg-gray-800 text-white w-full px-4 py-2 rounded border border-gray-700 focus:outline-none focus:border-primary" required>
                </div>
                
                <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="poster" class="block text-sm font-medium text-gray-300 mb-1">Poster Image</label>
                        <input type="file" name="poster" id="poster" accept="image/*" class="bg-gray-800 text-white w-full px-4 py-2 rounded border border-gray-700 focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Preview</label>
                        <div class="h-32 bg-gray-800 border border-gray-700 rounded flex items-center justify-center overflow-hidden">
                            <img id="poster-preview" class="max-h-full max-w-full hidden" alt="Poster Preview">
                            <span id="no-preview" class="text-gray-500 text-sm">No image selected</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-span-2 flex justify-end mt-6 space-x-3">
                    <button type="button" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded" onclick="closeMovieModal()">Cancel</button>
                    <button type="submit" id="movie-submit" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded flex items-center">
                        <i class="ri-save-line mr-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab === 'api') switchTab('api');
        initSearch();
    });

    function switchTab(tab) {
        document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
        document.querySelectorAll('button[id$="-tab"]').forEach(button => {
            button.classList.remove('border-primary', 'text-primary');
            button.classList.add('border-transparent', 'hover:text-gray-300');
        });
        document.getElementById(`${tab}-content`).classList.remove('hidden');
        document.getElementById(`${tab}-tab`).classList.remove('border-transparent', 'hover:text-gray-300');
        document.getElementById(`${tab}-tab`).classList.add('border-primary', 'text-primary');
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tab);
        window.history.replaceState({}, '', url);
        filterMovies(); // Re-apply search filter when switching tabs
    }

    function initSearch() {
        const searchInput = document.getElementById('movie-search');
        searchInput.addEventListener('input', filterMovies);
    }

    function filterMovies() {
        const searchInput = document.getElementById('movie-search');
        const query = searchInput.value.trim().toLowerCase();
        const activeTab = document.getElementById('local-content').classList.contains('hidden') ? 'api' : 'local';
        const movieCards = document.querySelectorAll(`#${activeTab}-content .movie-card`);
        const noResults = document.getElementById('search-no-results');
        let hasVisibleMovies = false;

        movieCards.forEach(card => {
            const title = card.getAttribute('data-title');
            const matches = !query || title.includes(query);
            card.classList.toggle('hidden', !matches);
            if (matches) hasVisibleMovies = true;
        });

        noResults.classList.toggle('hidden', hasVisibleMovies || !query);
    }

    function openMovieModal(mode, id = null) {
        const modal = document.getElementById('movie-modal');
        const form = document.getElementById('movie-form');
        const title = document.getElementById('movie-modal-title').querySelector('span');
        const submitBtn = document.getElementById('movie-submit');
        const posterPreview = document.getElementById('poster-preview');
        const noPreview = document.getElementById('no-preview');
        const methodInput = document.getElementById('form-method');

        form.reset();
        posterPreview.classList.add('hidden');
        noPreview.classList.remove('hidden');
        document.getElementById('movie-id').value = '';

        if (mode === 'add') {
            title.textContent = 'Add New Movie';
            submitBtn.innerHTML = '<i class="ri-add-line mr-1"></i> Create';
            form.action = '<?php echo e(route("admin.movies.store")); ?>';
            methodInput.value = 'POST';
        } else if (mode === 'edit' && id) {
            title.textContent = 'Edit Movie';
            submitBtn.innerHTML = '<i class="ri-save-line mr-1"></i> Update';
            form.action = `/admin/movies/${id}`;
            methodInput.value = 'PUT';

            fetch(`/admin/movies/${id}/edit`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('movie-id').value = data.movie.id;
                    document.getElementById('title').value = data.movie.title;
                    document.getElementById('description').value = data.movie.description || '';
                    document.getElementById('year').value = data.movie.year || '';
                    document.getElementById('rating').value = data.movie.rating || '';
                    document.getElementById('categories').value = data.categories;
                    if (data.movie.poster) {
                        posterPreview.src = data.movie.poster;
                        posterPreview.classList.remove('hidden');
                        noPreview.classList.add('hidden');
                    }
                } else {
                    showMessage('error', data.message);
                }
            })
            .catch(error => showMessage('error', `Failed to load movie data: ${error.message}`));
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden'; 
    }

    function closeMovieModal() {
        document.getElementById('movie-modal').classList.add('hidden');
        document.getElementById('movie-modal').classList.remove('flex');
        document.body.style.overflow = ''; 
    }

    document.getElementById('movie-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        
        const headers = new Headers();
        headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
        
        fetch(form.action, {
            method: 'POST',
            headers: headers,
            body: formData,
            redirect: 'manual' 
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP error! status: ${response.status} - ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showMessage('success', data.message);
                closeMovieModal();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showMessage('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('error', `Error: ${error.message}`);
        });
    });

    function toggleBan(id, isTmdb) {
        const button = document.querySelector(`button[data-id="${id}"][data-is-tmdb="${isTmdb}"]`);
        if (!button) return;

        const currentlyBanned = button.textContent.trim() === 'Unban';
        const newStatus = !currentlyBanned;

        fetch(`/admin/movies/toggle-ban/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                is_tmdb: isTmdb,
                is_banned: newStatus,
                tmdb_id: isTmdb ? id : null
            })
        })
        .then(response => {
            if (!response.ok) throw new Error(`Network crapped out: ${response.status}`);
            return response.json();
        })
        .then(data => {
            button.textContent = newStatus ? 'Unban' : 'Ban';
            button.classList.toggle('bg-red-500', !newStatus);
            button.classList.toggle('hover:bg-red-600', !newStatus);
            button.classList.toggle('bg-green-500', newStatus);
            button.classList.toggle('hover:bg-green-600', newStatus);

            const movieCard = button.closest('.movie-card');
            if (newStatus && !movieCard.querySelector('.absolute.top-2.right-2')) {
                const bannedLabel = document.createElement('div');
                bannedLabel.className = 'absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded';
                bannedLabel.textContent = 'BANNED';
                movieCard.querySelector('.relative').appendChild(bannedLabel);
            } else if (!newStatus) {
                const bannedLabel = movieCard.querySelector('.absolute.top-2.right-2');
                if (bannedLabel) bannedLabel.remove();
            }

            showMessage('success', data.message);
        })
        .catch(error => showMessage('error', `Error flipping ban status: ${error.message}`));
    }

    function deleteMovie(id) {
        if (!confirm('Are you sure? This movie’s gonna be deader than my grandma’s Wi-Fi.')) return;

        fetch(`/admin/movies/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`Network said no: ${response.status}`);
            return response.json();
        })
        .then(data => {
            const card = document.querySelector(`button[onclick="deleteMovie(${id})"]`).closest('.movie-card');
            card.remove();
            showMessage('success', data.message);
        })
        .catch(error => showMessage('error', `Error yeeting movie: ${error.message}`));
    }

    function showMessage(type, text) {
        const message = document.createElement('div');
        message.className = `bg-${type === 'success' ? 'green' : 'red'}-100 border border-${type === 'success' ? 'green' : 'red'}-400 text-${type === 'success' ? 'green' : 'red'}-700 px-4 py-3 rounded mb-4`;
        message.textContent = text;
        const container = document.querySelector('.container');
        container.insertBefore(message, container.querySelector('.mb-6'));
        setTimeout(() => message.remove(), 3000);
    }

    document.getElementById('poster').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('poster-preview');
        const noPreview = document.getElementById('no-preview');
        
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
            noPreview.classList.add('hidden');
        } else {
            preview.classList.add('hidden');
            noPreview.classList.remove('hidden');
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('components.layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Youcode\Herd\file-rouge\resources\views/admin/movies.blade.php ENDPATH**/ ?>