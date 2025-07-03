<x-app-layout>
    <div class="p-6 lg:p-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('projects.index') }}" class="text-slate-600 hover:text-slate-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">{{ $project->name }}</h1>
                    <div class="flex items-center gap-4 mt-1">
                        <span class="text-slate-500">{{ $project->project_code }}</span>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : 
                               ($project->status === 'planning' ? 'bg-blue-100 text-blue-800' : 
                               ($project->status === 'on_hold' ? 'bg-yellow-100 text-yellow-800' : 
                               ($project->status === 'completed' ? 'bg-purple-100 text-purple-800' : 'bg-red-100 text-red-800'))) }}">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $project->priority === 'critical' ? 'bg-red-100 text-red-800' : 
                               ($project->priority === 'high' ? 'bg-orange-100 text-orange-800' : 
                               ($project->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                            {{ ucfirst($project->priority) }} Priority
                        </span>
                    </div>
                </div>
                <div class="ml-auto">
                    <a href="{{ route('projects.edit', $project) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Project
                    </a>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="bg-white p-4 rounded-lg border border-slate-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-slate-700">Project Progress</span>
                    <span class="text-sm font-bold text-slate-900">{{ $project->progress_percentage }}%</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-3">
                    <div class="bg-indigo-600 h-3 rounded-full transition-all duration-300" style="width: {{ $project->progress_percentage }}%"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Project Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Project Information -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <div class="flex items-start justify-between mb-6">
                        <h2 class="text-xl font-semibold text-slate-800">Project Information</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            @if($project->description)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Description</label>
                                    <p class="text-slate-600">{{ $project->description }}</p>
                                </div>
                            @endif

                            @if($project->location)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Location</label>
                                    <p class="text-slate-600">{{ $project->location }}</p>
                                </div>
                            @endif

                            @if($project->plant)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Plant</label>
                                    <p class="text-slate-600">{{ $project->plant->name }}</p>
                                    @if($project->plant->location)
                                        <p class="text-xs text-slate-500">{{ $project->plant->location }}</p>
                                    @endif
                                </div>
                            @endif

                            @if($project->contract_number)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Contract Number</label>
                                    <p class="text-slate-600">{{ $project->contract_number }}</p>
                                    @if($project->contract_date)
                                        <p class="text-xs text-slate-500">{{ $project->contract_date->format('F j, Y') }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="space-y-4">
                            @if($project->start_date && $project->end_date)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Timeline</label>
                                    <p class="text-slate-600">{{ $project->start_date->format('M j, Y') }} - {{ $project->end_date->format('M j, Y') }}</p>
                                    @if($project->duration)
                                        <p class="text-xs text-slate-500">{{ $project->duration }} days</p>
                                    @endif
                                    @if($project->days_remaining !== null)
                                        <p class="text-xs {{ $project->is_overdue ? 'text-red-600' : 'text-slate-500' }}">
                                            @if($project->is_overdue)
                                                Overdue
                                            @else
                                                {{ $project->days_remaining }} days remaining
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            @endif

                            @if($project->budget)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Budget</label>
                                    <p class="text-slate-600">${{ number_format($project->budget, 2) }}</p>
                                    @if($project->actual_cost)
                                        <p class="text-xs text-slate-500">Spent: ${{ number_format($project->actual_cost, 2) }}</p>
                                        @if($project->budget_utilization)
                                            <p class="text-xs {{ $project->budget_utilization > 100 ? 'text-red-600' : 'text-slate-500' }}">
                                                {{ number_format($project->budget_utilization, 1) }}% utilized
                                            </p>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tags -->
                    @if($project->tags && count($project->tags) > 0)
                        <div class="mt-6 pt-6 border-t border-slate-200">
                            <label class="text-sm font-medium text-slate-700">Tags</label>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach($project->tags as $tag)
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm rounded-full">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Notes -->
                    @if($project->notes)
                        <div class="mt-6 pt-6 border-t border-slate-200">
                            <label class="text-sm font-medium text-slate-700">Notes</label>
                            <p class="text-slate-600 mt-1">{{ $project->notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Client Information -->
                @if($project->client_name)
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <h2 class="text-xl font-semibold text-slate-800 mb-6">Client Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="text-sm font-medium text-slate-700">Client Name</label>
                                <p class="text-slate-600">{{ $project->client_name }}</p>
                            </div>
                            @if($project->client_contact)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Contact Person</label>
                                    <p class="text-slate-600">{{ $project->client_contact }}</p>
                                </div>
                            @endif
                            @if($project->client_email)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Email</label>
                                    <p class="text-slate-600">{{ $project->client_email }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Project Manager -->
                @if($project->project_manager)
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <h2 class="text-xl font-semibold text-slate-800 mb-6">Project Manager</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="text-sm font-medium text-slate-700">Name</label>
                                <p class="text-slate-600">{{ $project->project_manager }}</p>
                            </div>
                            @if($project->project_manager_email)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Email</label>
                                    <p class="text-slate-600">{{ $project->project_manager_email }}</p>
                                </div>
                            @endif
                            @if($project->project_manager_phone)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Phone</label>
                                    <p class="text-slate-600">{{ $project->project_manager_phone }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Team Members -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-slate-800">Team Members</h2>
                        @can('update', $project)
                        <a href="{{ route('projects.team.index', $project) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                            Manage Team
                        </a>
                        @endcan
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($project->employees as $employee)
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg">
                                @if($employee->profile_photo_path)
                                    <img src="{{ asset('storage/' . $employee->profile_photo_path) }}" alt="{{ $employee->full_name }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-slate-800">{{ $employee->full_name }}</p>
                                    <p class="text-xs text-slate-500 capitalize">{{ $employee->pivot->role }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-slate-500 py-4 col-span-full">No team members assigned.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column: Statistics and Quick Actions -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 gap-4">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-600">Team Size</p>
                                <p class="text-3xl font-bold text-indigo-600">{{ $project->team_size }}</p>
                            </div>
                            <div class="p-3 bg-indigo-100 rounded-lg">
                                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    @if($project->milestones)
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Milestones</p>
                                    <p class="text-3xl font-bold text-green-600">{{ $project->completed_milestones_count }}/{{ $project->total_milestones_count }}</p>
                                </div>
                                <div class="p-3 bg-green-100 rounded-lg">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($project->workflows->count() > 0)
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-slate-600">Workflows</p>
                                    <p class="text-3xl font-bold text-purple-600">{{ $project->workflows->count() }}</p>
                                </div>
                                <div class="p-3 bg-purple-100 rounded-lg">
                                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        @can('update', $project)
                        <a href="{{ route('projects.team.index', $project) }}" class="block w-full text-left px-4 py-3 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                <span class="font-medium text-indigo-700">Manage Team</span>
                            </div>
                        </a>
                        @endcan

                        <a href="{{ route('workflows.index', ['project_id' => $project->id]) }}" class="block w-full text-left px-4 py-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                <span class="font-medium text-purple-700">View Workflows</span>
                            </div>
                        </a>

                        <a href="{{ route('projects.edit', $project) }}" class="block w-full text-left px-4 py-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                <span class="font-medium text-green-700">Edit Project</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Milestones -->
                @if($project->milestones && count($project->milestones) > 0)
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4">Milestones</h3>
                        <div class="space-y-3">
                            @foreach($project->milestones as $milestone)
                                <div class="flex items-center justify-between p-3 {{ $milestone['completed'] ? 'bg-green-50 border-green-200' : 'bg-slate-50 border-slate-200' }} border rounded-lg">
                                    <div class="flex items-center gap-3">
                                        @if($milestone['completed'])
                                            <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center text-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-6 h-6 rounded-full border-2 border-slate-300"></div>
                                        @endif
                                        <span class="font-medium {{ $milestone['completed'] ? 'text-green-800' : 'text-slate-700' }}">{{ $milestone['name'] }}</span>
                                    </div>
                                    @if(isset($milestone['date']))
                                        <span class="text-xs {{ $milestone['completed'] ? 'text-green-600' : 'text-slate-500' }}">{{ $milestone['date'] }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Risks -->
                @if($project->risks && count($project->risks) > 0)
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4">Risk Assessment</h3>
                        <div class="space-y-3">
                            @foreach($project->risks as $risk)
                                <div class="p-3 border rounded-lg
                                    {{ $risk['severity'] === 'high' ? 'bg-red-50 border-red-200' : 
                                       ($risk['severity'] === 'medium' ? 'bg-yellow-50 border-yellow-200' : 'bg-green-50 border-green-200') }}">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-medium 
                                            {{ $risk['severity'] === 'high' ? 'text-red-800' : 
                                               ($risk['severity'] === 'medium' ? 'text-yellow-800' : 'text-green-800') }}">
                                            {{ $risk['name'] }}
                                        </span>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $risk['severity'] === 'high' ? 'bg-red-100 text-red-800' : 
                                               ($risk['severity'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($risk['severity']) }}
                                        </span>
                                    </div>
                                    @if(isset($risk['mitigation']))
                                        <p class="text-xs text-slate-600">{{ $risk['mitigation'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>