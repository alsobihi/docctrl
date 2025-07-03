<x-app-layout>
    <div class="p-6 lg:p-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <h1 class="text-3xl font-bold text-slate-900">Plant Management</h1>
            @can('admin')
            <a href="{{ route('plants.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 flex items-center gap-2 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Add New Plant</span>
            </a>
            @endcan
        </div>

        <!-- Search and Filter Form -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-8">
            <form action="{{ route('plants.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <x-input-label for="search" :value="__('Search (Name, Location, Manager)')" />
                        <x-text-input id="search" class="block mt-1 w-full" type="text" name="search" :value="request('search')" placeholder="e.g., Main Plant, Dubai, John Doe" />
                    </div>
                    <div>
                        <x-input-label for="status" :value="__('Filter by Status')" />
                        <select name="status" id="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">All Status</option>
                            <option value="active" @selected(request('status') == 'active')>Active</option>
                            <option value="inactive" @selected(request('status') == 'inactive')>Inactive</option>
                            <option value="maintenance" @selected(request('status') == 'maintenance')>Maintenance</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <x-primary-button class="w-full md:w-auto justify-center">
                            {{ __('Filter') }}
                        </x-primary-button>
                        <a href="{{ route('plants.index') }}" class="ml-2 px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Plants Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($plants as $plant)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <!-- Plant Header with Logo -->
                    <div class="relative h-32 bg-gradient-to-br from-indigo-500 to-purple-600">
                        @if($plant->logo_path)
                            <div class="absolute bottom-4 left-4">
                                <img src="{{ asset('storage/' . $plant->logo_path) }}" alt="{{ $plant->name }} Logo" class="w-16 h-16 rounded-lg bg-white p-2 shadow-lg object-contain">
                            </div>
                        @else
                            <div class="absolute bottom-4 left-4 w-16 h-16 rounded-lg bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $plant->status === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($plant->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($plant->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Plant Content -->
                    <div class="p-6">
                        <div class="mb-4">
                            <h3 class="text-xl font-bold text-slate-900 mb-1">{{ $plant->name }}</h3>
                            @if($plant->location)
                                <p class="text-slate-500 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $plant->location }}
                                </p>
                            @endif
                        </div>

                        <!-- Plant Stats -->
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-indigo-600">{{ $plant->employees_count }}</div>
                                <div class="text-xs text-slate-500">Employees</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $plant->projects_count }}</div>
                                <div class="text-xs text-slate-500">Projects</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">{{ $plant->workflows_count }}</div>
                                <div class="text-xs text-slate-500">Workflows</div>
                            </div>
                        </div>

                        <!-- Manager Info -->
                        @if($plant->manager_name)
                            <div class="mb-4 p-3 bg-slate-50 rounded-lg">
                                <div class="text-sm font-medium text-slate-700">Plant Manager</div>
                                <div class="text-sm text-slate-600">{{ $plant->manager_name }}</div>
                                @if($plant->manager_email)
                                    <div class="text-xs text-slate-500">{{ $plant->manager_email }}</div>
                                @endif
                            </div>
                        @endif

                        <!-- Description -->
                        @if($plant->description)
                            <p class="text-sm text-slate-600 mb-4 line-clamp-2">{{ $plant->description }}</p>
                        @endif

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                            <a href="{{ route('plants.show', $plant) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                View Details
                            </a>
                            <div class="flex items-center gap-2">
                                @can('admin')
                                <a href="{{ route('plants.edit', $plant) }}" class="text-slate-600 hover:text-slate-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('plants.destroy', $plant) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this plant?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-slate-900">No plants found</h3>
                        <p class="mt-1 text-sm text-slate-500">Try adjusting your search or filter criteria.</p>
                        @can('admin')
                        <div class="mt-6">
                            <a href="{{ route('plants.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add New Plant
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($plants->hasPages())
            <div class="mt-8">
                {{ $plants->links() }}
            </div>
        @endif
    </div>
</x-app-layout>