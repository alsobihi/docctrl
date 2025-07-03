<x-app-layout>
    <div class="p-6 lg:p-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('employees.index') }}" class="text-slate-600 hover:text-slate-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div class="flex items-center gap-4">
                    @if($employee->profile_photo_path)
                        <img src="{{ asset('storage/' . $employee->profile_photo_path) }}" alt="{{ $employee->full_name }}" class="w-16 h-16 rounded-full border-4 border-white shadow-lg object-cover">
                    @else
                        <div class="w-16 h-16 rounded-full bg-slate-200 flex items-center justify-center">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900">{{ $employee->full_name }}</h1>
                        <div class="flex items-center gap-4 mt-1">
                            <span class="text-slate-500">{{ $employee->employee_code }}</span>
                            @if($employee->badge_number)
                                <span class="text-slate-500">Badge: {{ $employee->badge_number }}</span>
                            @endif
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $employee->employment_status === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($employee->employment_status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($employee->employment_status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="ml-auto">
                    <a href="{{ route('employees.edit', $employee) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Employee Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Personal Information -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h2 class="text-xl font-semibold text-slate-800 mb-6">Personal Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            @if($employee->position)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Position</label>
                                    <p class="text-slate-600">{{ $employee->position }}</p>
                                </div>
                            @endif

                            @if($employee->department)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Department</label>
                                    <p class="text-slate-600">{{ $employee->department }}</p>
                                </div>
                            @endif

                            @if($employee->hire_date)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Hire Date</label>
                                    <p class="text-slate-600">{{ $employee->hire_date->format('F j, Y') }}</p>
                                    @if($employee->years_of_service)
                                        <p class="text-xs text-slate-500">{{ $employee->years_of_service }} years of service</p>
                                    @endif
                                </div>
                            @endif

                            @if($employee->date_of_birth)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Date of Birth</label>
                                    <p class="text-slate-600">{{ $employee->date_of_birth->format('F j, Y') }}</p>
                                    @if($employee->age)
                                        <p class="text-xs text-slate-500">{{ $employee->age }} years old</p>
                                    @endif
                                </div>
                            @endif

                            @if($employee->nationality)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Nationality</label>
                                    <p class="text-slate-600">{{ $employee->nationality }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-4">
                            @if($employee->phone)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Phone</label>
                                    <p class="text-slate-600">{{ $employee->phone }}</p>
                                </div>
                            @endif

                            @if($employee->email)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Email</label>
                                    <p class="text-slate-600">{{ $employee->email }}</p>
                                </div>
                            @endif

                            @if($employee->address)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Address</label>
                                    <p class="text-slate-600">{{ $employee->address }}</p>
                                </div>
                            @endif

                            @if($employee->plant)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Plant</label>
                                    <p class="text-slate-600">{{ $employee->plant->name }}</p>
                                    @if($employee->plant->location)
                                        <p class="text-xs text-slate-500">{{ $employee->plant->location }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Skills -->
                    @if($employee->skills && count($employee->skills) > 0)
                        <div class="mt-6 pt-6 border-t border-slate-200">
                            <label class="text-sm font-medium text-slate-700">Skills</label>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach($employee->skills as $skill)
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm rounded-full">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Notes -->
                    @if($employee->notes)
                        <div class="mt-6 pt-6 border-t border-slate-200">
                            <label class="text-sm font-medium text-slate-700">Notes</label>
                            <p class="text-slate-600 mt-1">{{ $employee->notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Employment Details -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h2 class="text-xl font-semibold text-slate-800 mb-6">Employment Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($employee->contract_type)
                            <div>
                                <label class="text-sm font-medium text-slate-700">Contract Type</label>
                                <p class="text-slate-600 capitalize">{{ $employee->contract_type }}</p>
                                @if($employee->contract_end_date)
                                    <p class="text-xs text-slate-500">Ends: {{ $employee->contract_end_date->format('F j, Y') }}</p>
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $employee->contract_status === 'expired' ? 'bg-red-100 text-red-800' : 
                                           ($employee->contract_status === 'expiring_soon' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ ucfirst(str_replace('_', ' ', $employee->contract_status)) }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        @if($employee->salary)
                            <div>
                                <label class="text-sm font-medium text-slate-700">Salary</label>
                                <p class="text-slate-600">${{ number_format($employee->salary, 2) }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Emergency Contact -->
                @if($employee->emergency_contact_name)
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <h2 class="text-xl font-semibold text-slate-800 mb-6">Emergency Contact</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="text-sm font-medium text-slate-700">Name</label>
                                <p class="text-slate-600">{{ $employee->emergency_contact_name }}</p>
                            </div>
                            @if($employee->emergency_contact_phone)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Phone</label>
                                    <p class="text-slate-600">{{ $employee->emergency_contact_phone }}</p>
                                </div>
                            @endif
                            @if($employee->emergency_contact_relationship)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Relationship</label>
                                    <p class="text-slate-600">{{ $employee->emergency_contact_relationship }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Documents -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-slate-800">Documents</h2>
                        <a href="{{ route('employees.documents.create', $employee) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                            Add Document
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Document Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Expiry Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @forelse ($employee->documents as $document)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $document->documentType->name }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-500">{{ $document->expiry_date->format('d M Y') }}</td>
                                        <td class="px-6 py-4">
                                            @if ($document->expiry_date->isPast())
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Expired</span>
                                            @elseif ($document->expiry_date->isBetween(now(), now()->addDays($document->documentType->warning_period_days ?? 30)))
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Expiring Soon</span>
                                            @else
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Valid</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-medium">
                                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">No documents found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Quick Stats and Actions -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 gap-4">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-600">Total Documents</p>
                                <p class="text-3xl font-bold text-indigo-600">{{ $employee->documents->count() }}</p>
                            </div>
                            <div class="p-3 bg-indigo-100 rounded-lg">
                                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-600">Expired Documents</p>
                                <p class="text-3xl font-bold text-red-600">{{ $employee->expired_documents_count }}</p>
                            </div>
                            <div class="p-3 bg-red-100 rounded-lg">
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-600">Expiring Soon</p>
                                <p class="text-3xl font-bold text-yellow-600">{{ $employee->expiring_documents_count }}</p>
                            </div>
                            <div class="p-3 bg-yellow-100 rounded-lg">
                                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-600">Active Projects</p>
                                <p class="text-3xl font-bold text-green-600">{{ $employee->projects->count() }}</p>
                            </div>
                            <div class="p-3 bg-green-100 rounded-lg">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('employees.documents.create', $employee) }}" class="block w-full text-left px-4 py-3 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span class="font-medium text-indigo-700">Add Document</span>
                            </div>
                        </a>

                        <a href="{{ route('employees.documents.index', $employee) }}" class="block w-full text-left px-4 py-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="font-medium text-green-700">Manage Documents</span>
                            </div>
                        </a>

                        <a href="{{ route('process-workflow.create') }}?employee_id={{ $employee->id }}" class="block w-full text-left px-4 py-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                <span class="font-medium text-purple-700">Start Workflow</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- In Progress Workflows -->
                @if($employee->assignedWorkflows->where('status', 'in_progress')->count() > 0)
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4">In Progress Workflows</h3>
                        <div class="space-y-3">
                            @foreach($employee->assignedWorkflows->where('status', 'in_progress') as $item)
                                <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                                    <span class="font-medium text-slate-700">{{ $item->workflow->name }}</span>
                                    <a href="{{ route('process-workflow.show', ['employee' => $employee, 'workflow' => $item->workflow]) }}" class="text-sm text-indigo-600 hover:text-indigo-800">View</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Assigned Projects -->
                @if($employee->projects->count() > 0)
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4">Assigned Projects</h3>
                        <div class="space-y-3">
                            @foreach($employee->projects as $project)
                                <div class="p-3 bg-slate-50 rounded-lg">
                                    <p class="font-semibold text-slate-800">{{ $project->name }}</p>
                                    <p class="text-sm text-slate-500">{{ $project->project_code }}</p>
                                    <p class="text-xs text-slate-400 capitalize">{{ $project->pivot->role }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>