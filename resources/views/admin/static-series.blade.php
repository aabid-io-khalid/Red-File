{{-- resources/views/front-office/tv-shows.blade.php --}}
@extends('components.layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-white">TV Shows Management</h1>
        <button 
            x-data 
            @click="$dispatch('open-modal', 'create-series-modal')"
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
                @foreach($popularShows as $show)
                <tr class="border-b border-gray-700/50 hover:bg-gray-700/20 transition-colors">
                    <td class="p-4">
                        <img 
                            src="https://image.tmdb.org/t/p/w500{{ $show->poster_path }}" 
                            alt="{{ $show->name }}" 
                            class="w-16 h-24 object-cover rounded"
                        >
                    </td>
                    <td class="p-4">{{ $show->name }}</td>
                    <td class="p-4">{{ $showGenres[$show->id] ?? 'N/A' }}</td>
                    <td class="p-4">{{ $show->number_of_seasons ?? 'N/A' }} Seasons</td>
                    <td class="p-4">
                        <span class="text-green-500">Active</span>
                    </td>
                    <td class="p-4">
                        <div class="flex space-x-2">
                            <button 
                                @click="editSeries({{ $show->id }})"
                                class="text-blue-500 hover:text-blue-400"
                                title="Edit Series"
                            >
                                <i class="ri-edit-line"></i>
                            </button>
                            <button 
                                @click="deleteSeries({{ $show->id }})"
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

        {{-- Pagination --}}
        <div class="p-4">
            {{-- Pagination logic can be added --}}
        </div>
    </div>

    {{-- Create Series Modal --}}
    <div 
        x-data="{ isOpen: false }"
        x-show="isOpen"
        @open-modal.window="if ($event.detail === 'create-series-modal') isOpen = true"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        x-cloak
    >
        <div class="bg-gray-800 rounded-lg w-full max-w-2xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">Add New Series</h2>
                <button 
                    @click="isOpen = false"
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
                            @foreach($showGenres as $id => $genre)
                                <option value="{{ $id }}">{{ $genre }}</option>
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

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm text-gray-400 mb-2">First Air Date</label>
                        <input 
                            type="date" 
                            name="first_air_date" 
                            required 
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm text-gray-400 mb-2">Total Seasons</label>
                        <input 
                            type="number" 
                            name="total_seasons" 
                            min="1" 
                            required 
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm text-gray-400 mb-2">Poster</label>
                        <input 
                            type="file" 
                            name="poster" 
                            accept="image/*" 
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary file:mr-4 file:rounded-full file:border-0 file:bg-primary file:text-white file:px-4 file:py-2"
                        >
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm text-gray-400 mb-2">Network/Platform</label>
                        <input 
                            type="text" 
                            name="network" 
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                        >
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
    function editSeries(id) {
        console.log('Edit series:', id);
    }

    function deleteSeries(id) {
        console.log('Delete series:', id);
    }
</script>
@endpush