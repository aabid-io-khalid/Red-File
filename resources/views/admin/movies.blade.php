@extends('components.layouts.admin')

@section('content')
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

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($hasDbError)
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
            <strong>Warning:</strong> Could not connect to database. Some features may be limited. *Guess the server’s hungover again.*
        </div>
    @endif

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-700">
        <ul class="flex flex-wrap -mb-px">
            <li class="mr-2">
                <button class="inline-block p-4 border-b-2 {{ request('tab') != 'api' ? 'border-primary text-primary' : 'border-transparent hover:text-gray-300' }} rounded-t-lg" 
                    id="local-tab" onclick="switchTab('local')">
                    Local Movies
                </button>
            </li>
            <li class="mr-2">
                <button class="inline-block p-4 border-b-2 {{ request('tab') == 'api' ? 'border-primary text-primary' : 'border-transparent hover:text-gray-300' }} rounded-t-lg" 
                    id="api-tab" onclick="switchTab('api')">
                    TMDB API Movies
                </button>
            </li>
        </ul>
    </div>

    <!-- Local Movies Tab -->
    <div id="local-content" class="tab-content {{ request('tab') == 'api' ? 'hidden' : '' }}">
        @if($hasDbError)
            <div class="text-center py-6">
                <p class="text-gray-400">Database connection is unavailable. Cannot display local movies. *It’s probably plotting its escape.*</p>
            </div>
        @elseif(isset($localMovies) && count($localMovies) === 0)
            <div class="text-center py-6">
                <p class="text-gray-400">No movies created yet. Add your first movie!</p>
            </div>
        @elseif(isset($localMovies) && count($localMovies) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($localMovies as $movie)
                <div class="bg-dark rounded-lg overflow-hidden border border-gray-800 movie-card relative">
                    <div class="relative">
                        <img 
                            src="{{ $movie->poster ?? '/img/no-poster.jpg' }}" 
                            alt="{{ $movie->title }}"
                            class="w-full h-64 object-cover card-image transition-all duration-300"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-70"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-bold">{{ $movie->title }}</h3>
                            <div class="flex items-center mt-1">
                                <span class="text-yellow-500 mr-1"><i class="ri-star-fill"></i></span>
                                <span class="text-white text-sm">{{ $movie->rating ?? 'N/A' }}</span>
                                <span class="text-gray-400 text-sm ml-2">{{ $movie->year ?? 'N/A' }}</span>
                            </div>
                        </div>
                        @if($movie->is_banned)
                        <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                            BANNED
                        </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <p class="text-gray-300 text-sm line-clamp-3 h-18">{{ $movie->description ?? 'No description available.' }}</p>
                        </div>
                        <div class="mb-2">
                            <span class="text-gray-400 text-xs">Categories:</span>
                            <span class="text-gray-300 text-xs">{{ $movie->categories->pluck('name')->implode(', ') }}</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-gray-400 text-xs">Created:</span>
                            <span class="text-gray-300 text-xs">{{ $movie->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="mt-4 flex justify-between items-center">
                            <div>
                                <button 
                                    class="px-3 py-1 rounded text-sm {{ $movie->is_banned ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600' }} text-white ban-button"
                                    data-id="{{ $movie->id }}"
                                    data-is-tmdb="false"
                                    onclick="toggleBan({{ $movie->id }}, false)"
                                >
                                    {{ $movie->is_banned ? 'Unban' : 'Ban' }}
                                </button>
                            </div>
                            <div>
                                <button 
                                    onclick="openMovieModal('edit', {{ $movie->id }})" 
                                    class="text-blue-500 hover:text-blue-700 mr-2"
                                >
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button 
                                    onclick="deleteMovie({{ $movie->id }})" 
                                    class="text-red-500 hover:text-red-700"
                                >
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6">
                <p class="text-gray-400">No movies available. *Guess they all got lost in the void.*</p>
            </div>
        @endif
    </div>

    <!-- TMDB API Movies Tab -->
    <div id="api-content" class="tab-content {{ request('tab') == 'api' ? '' : 'hidden' }}">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @if(isset($apiMovies) && !empty($apiMovies))
                @foreach($apiMovies as $movie)
                <div class="bg-dark rounded-lg overflow-hidden border border-gray-800 movie-card relative">
                    <div class="relative">
                        <img 
                            src="{{ isset($movie['poster_path']) && $movie['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'] : '/img/no-poster.jpg' }}" 
                            alt="{{ $movie['title'] ?? 'Movie' }}"
                            class="w-full h-64 object-cover card-image transition-all duration-300"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-70"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-bold">{{ $movie['title'] ?? 'Unknown Title' }}</h3>
                            <div class="flex items-center mt-1">
                                <span class="text-yellow-500 mr-1"><i class="ri-star-fill"></i></span>
                                <span class="text-white text-sm">{{ isset($movie['vote_average']) ? number_format($movie['vote_average'], 1) : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-300 text-sm line-clamp-3 h-18">{{ $movie['overview'] ?? 'No description available.' }}</p>
                        <div class="mt-4 flex justify-between items-center">
                            <button 
                                class="px-3 py-1 rounded text-sm {{ isset($bannedApiMovieIds) && in_array($movie['id'], $bannedApiMovieIds) ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600' }} text-white ban-button"
                                data-id="{{ $movie['id'] }}"
                                data-is-tmdb="true"
                                data-tmdb-id="{{ $movie['id'] }}"
                                onclick="toggleBan({{ $movie['id'] }}, true)"
                            >
                                {{ isset($bannedApiMovieIds) && in_array($movie['id'], $bannedApiMovieIds) ? 'Unban' : 'Ban' }}
                            </button>
                        </div>
                    </div>
                    @if(isset($bannedApiMovieIds) && in_array($movie['id'], $bannedApiMovieIds))
                    <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                        BANNED
                    </div>
                    @endif
                </div>
                @endforeach
            @else
            <div class="col-span-full text-center py-6">
                <p class="text-gray-400">Unable to fetch movies from TMDB API. Please try again later. *Maybe it’s napping.*</p>
            </div>
            @endif
        </div>
        
        <!-- Pagination for TMDB API Movies -->
        @if(isset($apiMoviesPaginator) && $apiMoviesPaginator->lastPage() > 1)
        <div class="mt-8 flex justify-center">
            <nav class="flex items-center">
                @if($apiMoviesPaginator->onFirstPage())
                    <span class="px-3 py-1 bg-gray-800 text-gray-500 rounded-l cursor-not-allowed">Previous</span>
                @else
                    <a href="{{ $apiMoviesPaginator->previousPageUrl() }}" class="px-3 py-1 bg-gray-800 text-primary hover:bg-gray-700 rounded-l">Previous</a>
                @endif
                
                <div class="flex">
                    @php
                        $start = max($apiMoviesPaginator->currentPage() - 2, 1);
                        $end = min($start + 4, $apiMoviesPaginator->lastPage());
                        if ($end - $start < 4 && $start > 1) {
                            $start = max(1, $end - 4);
                        }
                    @endphp
                    
                    @if($start > 1)
                        <a href="{{ $apiMoviesPaginator->url(1) }}" class="px-3 py-1 bg-gray-800 text-white hover:bg-gray-700">1</a>
                        @if($start > 2)
                            <span class="px-2 py-1 bg-gray-800 text-gray-500">...</span>
                        @endif
                    @endif
                    
                    @for($i = $start; $i <= $end; $i++)
                        <a href="{{ $apiMoviesPaginator->url($i) }}" 
                           class="px-3 py-1 {{ $apiMoviesPaginator->currentPage() == $i ? 'bg-primary text-white' : 'bg-gray-800 text-white hover:bg-gray-700' }}">
                            {{ $i }}
                        </a>
                    @endfor
                    
                    @if($end < $apiMoviesPaginator->lastPage())
                        @if($end < $apiMoviesPaginator->lastPage() - 1)
                            <span class="px-2 py-1 bg-gray-800 text-gray-500">...</span>
                        @endif
                        <a href="{{ $apiMoviesPaginator->url($apiMoviesPaginator->lastPage()) }}" class="px-3 py-1 bg-gray-800 text-white hover:bg-gray-700">{{ $apiMoviesPaginator->lastPage() }}</a>
                    @endif
                </div>
                
                @if($apiMoviesPaginator->hasMorePages())
                    <a href="{{ $apiMoviesPaginator->nextPageUrl() }}" class="px-3 py-1 bg-gray-800 text-primary hover:bg-gray-700 rounded-r">Next</a>
                @else
                    <span class="px-3 py-1 bg-gray-800 text-gray-500 rounded-r cursor-not-allowed">Next</span>
                @endif
            </nav>
        </div>
        @endif
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
                @csrf
                <input type="hidden" name="id" id="movie-id">
                <input type="hidden" name="_method" id="form-method" value="POST">
                
                <div class="col-span-1">
                    <label for="title" class="block text-sm font-medium text-gray-300 mb-1">Title</label>
                    <input type="text" name="title" id="title" class="bg-gray-800 text-white w-full px-4 py-2 rounded border border-gray-700 focus:outline-none focus:border-primary" required>
                </div>
                
                <div class="col-span-1">
                    <label for="year" class="block text-sm font-medium text-gray-300 mb-1">Year</label>
                    <input type="number" name="year" id="year" min="1900" max="{{ date('Y') }}" class="bg-gray-800 text-white w-full px-4 py-2 rounded border border-gray-700 focus:outline-none focus:border-primary">
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
    }

    function openMovieModal(mode, id = null) {
        const modal = document.getElementById('movie-modal');
        const form = document.getElementById('movie-form');
        const title = document.getElementById('movie-modal-title').querySelector('span');
        const submitBtn = document.getElementById('movie-submit');
        const posterPreview = document.getElementById('poster-preview');
        const noPreview = document.getElementById('no-preview');
        const methodInput = document.getElementById('form-method');

        // Reset form and modal
        form.reset();
        posterPreview.classList.add('hidden');
        noPreview.classList.remove('hidden');
        document.getElementById('movie-id').value = '';

        if (mode === 'add') {
            title.textContent = 'Add New Movie';
            submitBtn.innerHTML = '<i class="ri-add-line mr-1"></i> Create';
            form.action = '{{ route("admin.movies.store") }}';
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
        document.body.style.overflow = 'hidden'; // Lock scroll
    }

    function closeMovieModal() {
        document.getElementById('movie-modal').classList.add('hidden');
        document.getElementById('movie-modal').classList.remove('flex');
        document.body.style.overflow = ''; // Unlock scroll
    }

    document.getElementById('movie-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    
    // Add proper Content-Type header
    const headers = new Headers();
    headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
    
    fetch(form.action, {
        method: 'POST',
        headers: headers,
        body: formData,
        redirect: 'manual' // Prevent redirects
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
@endsection