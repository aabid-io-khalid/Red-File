@extends('components.layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-white">Series Management</h1>
        <button 
            type="button"
            id="openCreateModal"
            class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors"
        >
            Add New Series
        </button>
    </div>

    {{-- Series Table --}}
    <div class="bg-gray-800/50 backdrop-blur-md rounded-lg overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-700/50">
                <tr>
                    <th class="p-4">Poster</th>
                    <th class="p-4">Title</th>
                    <th class="p-4">Genre</th>
                    <th class="p-4">Seasons</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($series as $serie)
                <tr class="border-b border-gray-700/50 hover:bg-gray-700/20 transition-colors">
                    <td class="p-4">
                        <img src="https://image.tmdb.org/t/p/w500{{ $serie->poster_path ?? '' }}" 
                             alt="{{ $serie->name ?? 'No title available' }}" 
                             class="w-16 h-24 object-cover rounded">
                    </td>
                    <td class="p-4">{{ $serie->name ?? 'No title available' }}</td>
                    <td class="p-4">
                        @if(!empty($serie->genres) && is_array($serie->genres))
                            @foreach($serie->genres as $genre)
                                {{ $genre->name ?? 'N/A' }}@if (!$loop->last) / @endif
                            @endforeach
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="p-4">
                        {{ $serie->number_of_seasons ?? 'N/A' }} Seasons
                    </td>
                    <td class="p-4">
                        <span class="{{ isset($serie->is_banned) && $serie->is_banned ? 'text-red-500' : 'text-green-500' }}">
                            {{ isset($serie->is_banned) && $serie->is_banned ? 'Banned' : 'Active' }}
                        </span>
                    </td>
                    <td class="p-4">
                        <div class="flex space-x-2">
                            <button 
                                onclick="editSeries({{ $serie->id }})"
                                class="text-blue-500 hover:text-blue-400"
                                title="Edit Series"
                            >
                                <i class="ri-edit-line"></i>
                            </button>
                            <form 
                                action="{{ route('admin.series.toggle-ban', $serie->id) }}" 
                                method="POST" 
                                class="inline"
                            >
                                @csrf
                                @method('PATCH')
                                <button 
                                    type="submit"
                                    class="{{ isset($serie->is_banned) && $serie->is_banned ? 'text-green-500 hover:text-green-400' : 'text-red-500 hover:text-red-400' }}"
                                    title="{{ isset($serie->is_banned) && $serie->is_banned ? 'Unban Series' : 'Ban Series' }}"
                                >
                                    <i class="ri-{{ isset($serie->is_banned) && $serie->is_banned ? 'play-line' : 'stop-line' }}"></i>
                                </button>
                            </form>
                            <button 
                                onclick="deleteSeries({{ $serie->id }})"
                                class="text-red-500 hover:text-red-400"
                                title="Delete Series"
                            >
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Inline Pagination --}}
        <div class="p-4">
            {{ $series->links() }} <!-- Pagination links -->
        </div>
    </div>

    {{-- Create Series Modal --}}
    <div id="createSeriesModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-gray-800 rounded-lg w-full max-w-2xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">Add New Series</h2>
                <button 
                    id="closeCreateModal"
                    class="text-gray-400 hover:text-white"
                >
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>

            <form 
                action="{{ route('admin.series.store') }}" 
                method="POST" 
                enctype="multipart/form-data"
            >
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    {{-- Series Details Inputs --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm text-gray-400 mb-2">Title</label>
                        <input 
                            type="text" 
                            name="title" 
                            required 
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm text-gray-400 mb-2">Genre</label>
                        <select 
                            name="genre" 
                            required 
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                            <option value="">Select Genre</option>
                            @foreach ($showGenres as $genre)
                                <option value="{{ strtolower($genre) }}">{{ $genre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm text-gray-400 mb-2">Description</label>
                        <textarea 
                            name="description" 
                            required 
                            rows="4" 
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                        ></textarea>
                    </div>

                    <div class="col-span-2">
                        <button 
                            type="submit" 
                            class="w-full bg-primary text-white py-3 rounded-lg hover:bg-primary-dark transition-colors"
                        >
                            Create Series
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
    // Modal control
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('createSeriesModal');
        const openBtn = document.getElementById('openCreateModal');
        const closeBtn = document.getElementById('closeCreateModal');
        
        openBtn.addEventListener('click', function() {
            modal.classList.remove('hidden');
        });
        
        closeBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
        });
    });

    // Series actions
    function editSeries(id) {
        console.log('Edit series:', id);
    }
    
    function deleteSeries(id) {
        if (confirm('Are you sure you want to delete this series?')) {
            console.log('Delete series:', id);
        }
    }
</script>
@endpush