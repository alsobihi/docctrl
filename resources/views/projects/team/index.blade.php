<x-app-layout>
    <div class="p-6 lg:p-8">
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('projects.show', $project) }}" class="text-slate-600 hover:text-slate-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-slate-900">Manage Project Team</h1>
            </div>
            <p class="text-slate-500 ml-10">For Project: <span class="font-semibold">{{ $project->name }}</span></p>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Add Member Form -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h2 class="text-xl font-semibold mb-4">Add Team Member</h2>
                    <form action="{{ route('projects.team.store', $project) }}" method="POST">
                        @csrf
                        <div>
                            <x-input-label for="employee_id" :value="__('Employee')" />
                            <select name="employee_id" id="employee_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Select an employee...</option>
                                @foreach($availableEmployees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->first_name }} {{ $employee->last_name }}
                                        @if($employee->position)
                                            ({{ $employee->position }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Role on Project')" />
                            <select name="role" id="role" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="member">Team Member</option>
                                <option value="manager">Project Manager</option>
                                <option value="lead">Team Lead</option>
                                <option value="engineer">Engineer</option>
                                <option value="designer">Designer</option>
                                <option value="developer">Developer</option>
                                <option value="analyst">Analyst</option>
                                <option value="consultant">Consultant</option>
                                <option value="specialist">Specialist</option>
                                <option value="coordinator">Coordinator</option>
                                <option value="supervisor">Supervisor</option>
                            </select>
                        </div>
                        <div class="mt-6">
                            <x-primary-button>{{ __('Add to Team') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Side: Current Team List -->
            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h2 class="text-xl font-semibold mb-6">Current Team</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($project->employees as $employee)
                            <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-lg border border-slate-200">
                                @if($employee->profile_photo_path)
                                    <img src="{{ asset('storage/' . $employee->profile_photo_path) }}" alt="{{ $employee->full_name }}" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-slate-200 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <p class="font-medium text-slate-800">{{ $employee->full_name }}</p>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-xs text-slate-500 capitalize">{{ $employee->pivot->role }}</p>
                                            @if($employee->position)
                                                <p class="text-xs text-slate-400">{{ $employee->position }}</p>
                                            @endif
                                        </div>
                                        <form action="{{ route('projects.team.destroy', ['project' => $project, 'employee' => $employee]) }}" method="POST" onsubmit="return confirm('Remove this member?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Remove</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8 text-slate-500">
                                <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-slate-900">No team members assigned</h3>
                                <p class="mt-1 text-sm text-slate-500">Get started by adding team members to this project.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Team Roles Distribution -->
                @if($project->employees->count() > 0)
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mt-6">
                        <h2 class="text-xl font-semibold mb-6">Team Composition</h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @php
                                $roleCount = $project->employees->groupBy('pivot.role')->map->count();
                                $totalMembers = $project->employees->count();
                            @endphp
                            
                            @foreach($roleCount as $role => $count)
                                <div class="text-center p-4 bg-slate-50 rounded-lg">
                                    <p class="text-2xl font-bold text-indigo-600">{{ $count }}</p>
                                    <p class="text-sm text-slate-600 capitalize">{{ $role }}{{ $count > 1 ? 's' : '' }}</p>
                                    <div class="w-full bg-slate-200 rounded-full h-1.5 mt-2">
                                        <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ ($count / $totalMembers) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>