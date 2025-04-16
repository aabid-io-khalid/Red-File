

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-white">TV Shows Management</h1>
        <button 
            type="button" 
            class="bg-primary hover:bg-primary/90 px-4 py-2 rounded text-white flex items-center"
            onclick="openTvShowModal('add')"
        >
            <i class="ri-add-line mr-1"></i> Add New TV Show
        </button>
    </div>

    <!-- Search Bar -->
    <div class="mb-6">
        <div class="relative">
            <input 
                type="text" 
                id="series-search" 
                placeholder="Search TV shows by title..." 
                class="bg-gray-800 text-white w-full px-4 py-2 rounded-lg border border-gray-700 focus:outline-none focus:border-primary pl-10"
            >
            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>
        <div id="search-no-results" class="hidden text-center text-gray-400 mt-4">
            No TV shows found matching your search.
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

    <?php if($hasDbError ?? false): ?>
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
            <strong>Warning:</strong> Could not connect to database. Some features may be limited. *Guess the server’s binge-watching its own downtime.*
        </div>
    <?php endif; ?>

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-700">
        <ul class="flex flex-wrap -mb-px">
            <li class="mr-2">
                <button class="inline-block p-4 border-b-2 <?php echo e(request('tab') != 'api' ? 'border-primary text-primary' : 'border-transparent hover:text-gray-300'); ?> rounded-t-lg" 
                    id="local-tab" onclick="switchTab('local')">
                    Local TV Shows
                </button>
            </li>
            <li class="mr-2">
                <button class="inline-block p-4 border-b-2 <?php echo e(request('tab') == 'api' ? 'border-primary text-primary' : 'border-transparent hover:text-gray-300'); ?> rounded-t-lg" 
                    id="api-tab" onclick="switchTab('api')">
                    TMDB API TV Shows
                </button>
            </li>
        </ul>
    </div>

    <!-- Local TV Shows Tab -->
    <div id="local-content" class="tab-content <?php echo e(request('tab') == 'api' ? 'hidden' : ''); ?>">
        <?php if($hasDbError ?? false): ?>
            <div class="text-center py-6">
                <p class="text-gray-400">Database connection is unavailable. Cannot display local TV shows. *It’s probably off filming its own spin-off.*</p>
            </div>
        <?php elseif($localSeries->isEmpty()): ?>
            <div class="text-center py-6">
                <p class="text-gray-400">No TV shows created yet. Add your first TV show!</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php $__currentLoopData = $localSeries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $series): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-dark rounded-lg overflow-hidden border border-gray-800 series-card relative" data-title="<?php echo e(strtolower($series->title)); ?>">
                    <div class="relative">
                        <img 
                            src="<?php echo e($series->poster ?? '/img/no-poster.jpg'); ?>" 
                            alt="<?php echo e($series->title); ?>"
                            class="w-full h-64 object-cover card-image transition-all duration-300"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-70"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-bold"><?php echo e($series->title); ?></h3>
                            <div class="flex items-center mt-1">
                                <span class="text-yellow-500 mr-1"><i class="ri-star-fill"></i></span>
                                <span class="text-white text-sm"><?php echo e($series->rating ?? 'N/A'); ?></span>
                                <?php if($series->year): ?>
                                <span class="text-gray-400 text-sm ml-2"><?php echo e($series->year); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if($series->is_banned): ?>
                        <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                            BANNED
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <p class="text-gray-300 text-sm line-clamp-3 h-18"><?php echo e($series->description ?? 'No description available.'); ?></p>
                        </div>
                        <?php if($series->categories->isNotEmpty()): ?>
                        <div class="mb-2">
                            <span class="text-gray-400 text-xs">Categories:</span>
                            <span class="text-gray-300 text-xs"><?php echo e($series->categories->pluck('name')->implode(', ')); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($series->seasons): ?>
                        <div class="mb-2">
                            <span class="text-gray-400 text-xs">Seasons:</span>
                            <span class="text-gray-300 text-xs"><?php echo e($series->seasons); ?></span>
                            <?php if($series->episodes_per_season): ?>
                            <span class="text-gray-400 text-xs ml-2">Eps/Season:</span>
                            <span class="text-gray-300 text-xs"><?php echo e($series->episodes_per_season); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <div class="mb-2">
                            <span class="text-gray-400 text-xs">Created:</span>
                            <span class="text-gray-300 text-xs"><?php echo e($series->created_at->format('M d, Y')); ?></span>
                        </div>
                        <div class="mt-4 flex justify-between items-center">
                            <div>
                                <button 
                                    class="px-3 py-1 rounded text-sm <?php echo e($series->is_banned ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'); ?> text-white ban-button"
                                    data-id="<?php echo e($series->id); ?>"
                                    data-is-tmdb="false"
                                    onclick="toggleBan(<?php echo e($series->id); ?>, false)"
                                >
                                    <?php echo e($series->is_banned ? 'Unban' : 'Ban'); ?>

                                </button>
                            </div>
                            <div>
                                <button 
                                    onclick="editTvShow(<?php echo e($series->id); ?>)" 
                                    class="text-blue-500 hover:text-blue-700 mr-2"
                                >
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button 
                                    onclick="deleteTvShow(<?php echo e($series->id); ?>)" 
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
        <?php endif; ?>
    </div>

    <!-- TMDB API TV Shows Tab -->
    <div id="api-content" class="tab-content <?php echo e(request('tab') == 'api' ? '' : 'hidden'); ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php if(!empty($apiSeries)): ?>
                <?php $__currentLoopData = $apiSeries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $series): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-dark rounded-lg overflow-hidden border border-gray-800 series-card relative" data-title="<?php echo e(strtolower($series['name'] ?? 'unknown title')); ?>">
                    <div class="relative">
                        <img 
                            src="<?php echo e($series['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $series['poster_path'] : '/img/no-poster.jpg'); ?>" 
                            alt="<?php echo e($series['name'] ?? 'TV Show'); ?>"
                            class="w-full h-64 object-cover card-image transition-all duration-300"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-70"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-bold"><?php echo e($series['name'] ?? 'Unknown Title'); ?></h3>
                            <div class="flex items-center mt-1">
                                <span class="text-yellow-500 mr-1"><i class="ri-star-fill"></i></span>
                                <span class="text-white text-sm"><?php echo e($series['vote_average'] ? number_format($series['vote_average'], 1) : 'N/A'); ?></span>
                                <?php if($series['first_air_date']): ?>
                                <span class="text-gray-400 text-sm ml-2"><?php echo e(substr($series['first_air_date'], 0, 4)); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if(in_array($series['id'], $bannedApiSeriesIds ?? [])): ?>
                        <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                            BANNED
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-300 text-sm line-clamp-3 h-18"><?php echo e($series['overview'] ?? 'No description available.'); ?></p>
                        <div class="mt-4 flex justify-between items-center">
                            <button 
                                class="px-3 py-1 rounded text-sm <?php echo e(in_array($series['id'], $bannedApiSeriesIds ?? []) ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'); ?> text-white ban-button"
                                data-id="<?php echo e($series['id']); ?>"
                                data-is-tmdb="true"
                                onclick="toggleBan(<?php echo e($series['id']); ?>, true)"
                            >
                                <?php echo e(in_array($series['id'], $bannedApiSeriesIds ?? []) ? 'Unban' : 'Ban'); ?>

                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <div class="col-span-full text-center py-6">
                <p class="text-gray-400">Unable to fetch TV shows from TMDB API. *Maybe it’s on a season break.*</p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination for TMDB API TV Shows -->
        <?php if($apiSeriesPaginator && $apiSeriesPaginator->lastPage() > 1): ?>
        <div class="mt-8 flex justify-center">
            <nav class="flex items-center">
                <?php if($apiSeriesPaginator->onFirstPage()): ?>
                    <span class="px-3 py-1 bg-gray-800 text-gray-500 rounded-l cursor-not-allowed">Previous</span>
                <?php else: ?>
                    <a href="<?php echo e($apiSeriesPaginator->previousPageUrl()); ?>" class="px-3 py-1 bg-gray-800 text-primary hover:bg-gray-700 rounded-l">Previous</a>
                <?php endif; ?>
                
                <div class="flex">
                    <?php
                        $start = max($apiSeriesPaginator->currentPage() - 2, 1);
                        $end = min($start + 4, $apiSeriesPaginator->lastPage());
                        if ($end - $start < 4 && $start > 1) {
                            $start = max(1, $end - 4);
                        }
                    ?>
                    
                    <?php if($start > 1): ?>
                        <a href="<?php echo e($apiSeriesPaginator->url(1)); ?>" class="px-3 py-1 bg-gray-800 text-white hover:bg-gray-700">1</a>
                        <?php if($start > 2): ?>
                            <span class="px-2 py-1 bg-gray-800 text-gray-500">...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for($i = $start; $i <= $end; $i++): ?>
                        <a href="<?php echo e($apiSeriesPaginator->url($i)); ?>" 
                           class="px-3 py-1 <?php echo e($apiSeriesPaginator->currentPage() == $i ? 'bg-primary text-white' : 'bg-gray-800 text-white hover:bg-gray-700'); ?>">
                            <?php echo e($i); ?>

                        </a>
                    <?php endfor; ?>
                    
                    <?php if($end < $apiSeriesPaginator->lastPage()): ?>
                        <?php if($end < $apiSeriesPaginator->lastPage() - 1): ?>
                            <span class="px-2 py-1 bg-gray-800 text-gray-500">...</span>
                        <?php endif; ?>
                        <a href="<?php echo e($apiSeriesPaginator->url($apiSeriesPaginator->lastPage())); ?>" class="px-3 py-1 bg-gray-800 text-white hover:bg-gray-700"><?php echo e($apiSeriesPaginator->lastPage()); ?></a>
                    <?php endif; ?>
                </div>
                
                <?php if($apiSeriesPaginator->hasMorePages()): ?>
                    <a href="<?php echo e($apiSeriesPaginator->nextPageUrl()); ?>" class="px-3 py-1 bg-gray-800 text-primary hover:bg-gray-700 rounded-r">Next</a>
                <?php else: ?>
                    <span class="px-3 py-1 bg-gray-800 text-gray-500 rounded-r cursor-not-allowed">Next</span>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>
    </div>

    <!-- Add/Edit TV Show Modal -->
    <div id="tv-show-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50 overflow-y-auto">
        <div class="bg-dark rounded-lg w-full max-w-2xl p-6 m-4 relative">
            <button onclick="closeTvShowModal()" class="absolute top-4 right-4 text-gray-400 hover:text-white">
                <i class="ri-close-line text-xl"></i>
            </button>
            <h2 id="modal-title" class="text-2xl font-semibold mb-6 text-white flex items-center">
                <i class="ri-tv-line mr-2"></i>
                <span>Add New TV Show</span>
            </h2>
            <form id="tv-show-form" action="<?php echo e(route('admin.series.store')); ?>" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" id="form-method" value="POST">
                <input type="hidden" name="id" id="series-id">
                
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
                
                <div class="col-span-1">
                    <label for="rating" class="block text-sm font-medium text-gray-300 mb-1">Rating (0-10)</label>
                    <input type="number" name="rating" id="rating" min="0" max="10" step="0.1" class="bg-gray-800 text-white w-full px-4 py-2 rounded border border-gray-700 focus:outline-none focus:border-primary">
                </div>
                
                <div class="col-span-1">
                    <label for="categories" class="block text-sm font-medium text-gray-300 mb-1">Categories (comma-separated)</label>
                    <input type="text" name="categories" id="categories" class="bg-gray-800 text-white w-full px-4 py-2 rounded border border-gray-700 focus:outline-none focus:border-primary" required>
                </div>
                
                <div class="col-span-1">
                    <label for="seasons" class="block text-sm font-medium text-gray-300 mb-1">Seasons</label>
                    <input type="number" name="seasons" id="seasons" min="1" class="bg-gray-800 text-white w-full px-4 py-2 rounded border border-gray-700 focus:outline-none focus:border-primary">
                </div>
                
                <div class="col-span-1">
                    <label for="episodes_per_season" class="block text-sm font-medium text-gray-300 mb-1">Episodes per Season</label>
                    <input type="number" name="episodes_per_season" id="episodes_per_season" min="1" class="bg-gray-800 text-white w-full px-4 py-2 rounded border border-gray-700 focus:outline-none focus:border-primary">
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
                    <button type="button" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded" onclick="closeTvShowModal()">Cancel</button>
                    <button type="submit" id="tv-show-submit" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded flex items-center">
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

    document.getElementById('tv-show-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
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
                closeTvShowModal();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showMessage('error', data.message);
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('error', `Error: ${error.message}`);
        });
    });
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
    filterSeries(); // Re-apply search filter when switching tabs
}

function initSearch() {
    const searchInput = document.getElementById('series-search');
    searchInput.addEventListener('input', filterSeries);
}

function filterSeries() {
    const searchInput = document.getElementById('series-search');
    const query = searchInput.value.trim().toLowerCase();
    const activeTab = document.getElementById('local-content').classList.contains('hidden') ? 'api' : 'local';
    const seriesCards = document.querySelectorAll(`#${activeTab}-content .series-card`);
    const noResults = document.getElementById('search-no-results');
    let hasVisibleSeries = false;

    seriesCards.forEach(card => {
        const title = card.getAttribute('data-title');
        const matches = !query || title.includes(query);
        card.classList.toggle('hidden', !matches);
        if (matches) hasVisibleSeries = true;
    });

    noResults.classList.toggle('hidden', hasVisibleSeries || !query);
}

function openTvShowModal(mode, id = null) {
    const modal = document.getElementById('tv-show-modal');
    const form = document.getElementById('tv-show-form');
    const title = document.getElementById('modal-title').querySelector('span');
    const submitBtn = document.getElementById('tv-show-submit');
    const posterPreview = document.getElementById('poster-preview');
    const noPreview = document.getElementById('no-preview');
    const methodInput = document.getElementById('form-method');

    form.reset();
    posterPreview.classList.add('hidden');
    noPreview.classList.remove('hidden');
    document.getElementById('series-id').value = '';

    if (mode === 'add') {
        title.textContent = 'Add New TV Show';
        submitBtn.innerHTML = '<i class="ri-add-line mr-1"></i> Create';
        form.action = '<?php echo e(route("admin.series.store")); ?>';
        methodInput.value = 'POST';
    } else if (mode === 'edit' && id) {
        title.textContent = 'Edit TV Show';
        submitBtn.innerHTML = '<i class="ri-save-line mr-1"></i> Update';
        form.action = `/admin/series/${id}`;
        methodInput.value = 'PUT';

        fetch(`/admin/series/${id}/edit`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('series-id').value = data.tvShow.id;
                document.getElementById('title').value = data.tvShow.title;
                document.getElementById('description').value = data.tvShow.description || '';
                document.getElementById('year').value = data.tvShow.year || '';
                document.getElementById('rating').value = data.tvShow.rating || '';
                document.getElementById('seasons').value = data.tvShow.seasons || '';
                document.getElementById('episodes_per_season').value = data.tvShow.episodes_per_season || '';
                document.getElementById('categories').value = data.categories;
                if (data.tvShow.poster) {
                    posterPreview.src = data.tvShow.poster;
                    posterPreview.classList.remove('hidden');
                    noPreview.classList.add('hidden');
                }
            } else {
                showMessage('error', data.message);
            }
        })
        .catch(error => showMessage('error', `Failed to load TV show data: ${error.message}`));
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeTvShowModal() {
    document.getElementById('tv-show-modal').classList.add('hidden');
    document.getElementById('tv-show-modal').classList.remove('flex');
    document.body.style.overflow = '';
}

function editTvShow(id) {
    openTvShowModal('edit', id);
}

function deleteTvShow(id) {
    if (!confirm('Are you sure? This TV show’s gonna be deader than a cancelled pilot.')) return;

    fetch(`/admin/series/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) throw new Error(`Network said no: ${response.status}`);
        return response.json();
    })
    .then(data => {
        const card = document.querySelector(`button[onclick="deleteTvShow(${id})"]`).closest('.series-card');
        card.remove();
        showMessage('success', data.message);
    })
    .catch(error => showMessage('error', `Error yeeting TV show: ${error.message}`));
}

function toggleBan(id, isTmdb) {
    const button = document.querySelector(`button[data-id="${id}"][data-is-tmdb="${isTmdb}"]`);
    if (!button) return;

    const currentlyBanned = button.textContent.trim() === 'Unban';
    const newStatus = !currentlyBanned;

    fetch(`/admin/series/toggle-ban${isTmdb ? '-api' : ''}/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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

        const seriesCard = button.closest('.series-card');
        if (newStatus && !seriesCard.querySelector('.absolute.top-2.right-2')) {
            const bannedLabel = document.createElement('div');
            bannedLabel.className = 'absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded';
            bannedLabel.textContent = 'BANNED';
            seriesCard.querySelector('.relative').appendChild(bannedLabel);
        } else if (!newStatus) {
            const bannedLabel = seriesCard.querySelector('.absolute.top-2.right-2');
            if (bannedLabel) bannedLabel.remove();
        }

        showMessage('success', data.message);
    })
    .catch(error => showMessage('error', `Error flipping ban status: ${error.message}`));
}

function showMessage(type, text) {
    const message = document.createElement('div');
    message.className = `bg-${type === 'success' ? 'green' : 'red'}-100 border border-${type === 'success' ? 'green' : 'red'}-400 text-${type === 'success' ? 'green' : 'red'}-700 px-4 py-3 rounded mb-4`;
    message.textContent = text;
    const container = document.querySelector('.container');
    container.insertBefore(message, container.querySelector('.mb-6'));
    setTimeout(() => message.remove(), 3000);
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('components.layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Youcode\Herd\file-rouge\resources\views/admin/series.blade.php ENDPATH**/ ?>