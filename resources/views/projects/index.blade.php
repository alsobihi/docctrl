<x-app-layout>
    <div class="p-6 lg:p-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <h1 class="text-3xl font-bold text-slate-900">Project Management</h1>
            <a href="{{ route('projects.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 flex items-center gap-2 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Add New Project</span>
            </a>
        </div>

        <!-- Advanced Search and Filter Form -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mb-8">
            <form action="{{ route('projects.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div class="md:col-span-2">
                        <x-input-label for="search" :value="__('Search')" />
                        <x-text-input id="search" class="block mt-1 w-full" type="text" name="search" :value="request('search')" placeholder="Name, code, client, manager..." />
                    </div>
                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select name="status" id="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">All Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="priority" :value="__('Priority')" />
                        <select name="priority" id="priority" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">All Priorities</option>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority }}" @selected(request('priority') == $priority)>{{ ucfirst($priority) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="plant_id" :value="__('Plant')" />
                        <select name="plant_id" id="plant_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">All Plants</option>
                            @foreach($plants as $plant)
                                <option value="{{ $plant->id }}" @selected(request('plant_id') == $plant->id)>{{ $plant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <x-primary-button class="w-full md:w-auto justify-center">
                            {{ __('Filter') }}
                        </x-primary-button>
                        <a href="{{ route('projects.index') }}" class="ml-2 px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Project Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($projects as $project)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <!-- Project Header -->
                    <div class="relative h-32 bg-gradient-to-br from-{{ $project->status_color }}-500 to-{{ $project->status_color }}-600">
                        <div class="absolute top-4 left-4">
                            <h3 class="text-white font-bold text-lg">{{ $project->name }}</h3>
                            <p class="text-white/80 text-sm">{{ $project->project_code }}</p>
                        </div>
                        
                        <!-- Status and Priority Badges -->
                        <div class="absolute top-4 right-4 flex flex-col gap-2">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-white/20 text-white backdrop-blur-sm">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $project->priority === 'critical' ? 'bg-red-100 text-red-800' : 
                                   ($project->priority === 'high' ? 'bg-orange-100 text-orange-800' : 
                                   ($project->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                {{ ucfirst($project->priority) }}
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="absolute bottom-4 left-4 right-4">
                            <div class="flex items-center justify-between text-white text-xs mb-1">
                                <span>Progress</span>
                                <span>{{ $project->progress_percentage }}%</span>
                            </div>
                            <div class="w-full bg-white/20 rounded-full h-2">
                                <div class="bg-white h-2 rounded-full transition-all duration-300" style="width: {{ $project->progress_percentage }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Project Content -->
                    <div class="p-6">
                        <!-- Description -->
                        @if($project->description)
                            <p class="text-sm text-slate-600 mb-4 line-clamp-2">{{ $project->description }}</p>
                        @endif

                        <!-- Client Info -->
                        @if($project->client_name)
                            <div class="mb-4 p-3 bg-slate-50 rounded-lg">
                                <div class="text-sm font-medium text-slate-700">Client</div>
                                <div class="text-sm text-slate-600">{{ $project->client_name }}</div>
                                @if($project->client_contact)
                                    <div class="text-xs text-slate-500">{{ $project->client_contact }}</div>
                                @endif
                            </div>
                        @endif

                        <!-- Project Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center">
                                <div class="text-lg font-bold text-indigo-600">{{ $project->team_size }}</div>
                                <div class="text-xs text-slate-500">Team Members</div>
                            </div>
                            <div class="text-center">
                                @if($project->budget)
                                    <div class="text-lg font-bold text-green-600">${{ number_format($project->budget / 1000, 0) }}K</div>
                                    <div class="text-xs text-slate-500">Budget</div>
                                @else
                                    <div class="text-lg font-bold text-slate-400">-</div>
                                    <div class="text-xs text-slate-500">Budget</div>
                                @endif
                            </div>
                        </div>

                        <!-- Timeline -->
                        @if($project->start_date && $project->end_date)
                            <div class="mb-4 p-3 bg-slate-50 rounded-lg">
                                <div class="text-sm font-medium text-slate-700 mb-1">Timeline</div>
                                <div class="text-xs text-slate-600">
                                    {{ $project->start_date->format('M j') }} - {{ $project->end_date->format('M j, Y') }}
                                </div>
                                @if($project->days_remaining !== null)
                                    <div class="text-xs {{ $project->is_overdue ? 'text-red-600' : 'text-slate-500' }}">
                                        @if($project->is_overdue)
                                            Overdue
                                        @else
                                            {{ $project->days_remaining }} days remaining
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Project Manager -->
                        @if($project->project_manager)
                            <div class="mb-4">
                                <div class="flex items-center gap-2 text-sm text-slate-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $project->project_manager }}
                                </div>
                            </div>
                        @endif

                        <!-- Tags -->
                        @if($project->tags && count($project->tags) > 0)
                            <div class="mb-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($project->tags, 0, 3) as $tag)
                                        <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs rounded-full">{{ $tag }}</span>
                                    @endforeach
                                    @if(count($project->tags) > 3)
                                        <span class="px-2 py-1 bg-slate-100 text-slate-600 text-xs rounded-full">+{{ count($project->tags) - 3 }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                            <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                View Details
                            </a>
                            <div class="flex items-center gap-2">
                                @can('update', $project)
                                <a href="{{ route('projects.team.index', $project) }}" class="text-green-600 hover:text-green-900 text-sm">
                                    Team
                                </a>
                                @endcan
                                <a href="{{ route('projects.edit', $project) }}" class="text-slate-600 hover:text-slate-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-slate-900">No projects found</h3>
                        <p class="mt-1 text-sm text-slate-500">Try adjusting your search or filter criteria.</p>
                        <div class="mt-6">
                            <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add New Project
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($projects->hasPages())
            <div class="mt-8">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</x-app-layout>