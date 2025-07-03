<x-app-layout>
    <div class="p-6 lg:p-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('plants.index') }}" class="text-slate-600 hover:text-slate-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-slate-900">{{ $plant->name }}</h1>
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                    {{ $plant->status === 'active' ? 'bg-green-100 text-green-800' : 
                       ($plant->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                    {{ ucfirst($plant->status) }}
                </span>
            </div>
            
            @if($plant->location)
                <p class="text-slate-500 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    {{ $plant->location }}
                </p>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Plant Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Plant Information Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <div class="flex items-start justify-between mb-6">
                        <h2 class="text-xl font-semibold text-slate-800">Plant Information</h2>
                        @can('admin')
                        <a href="{{ route('plants.edit', $plant) }}" class="text-indigo-600 hover:text-indigo-900 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>
                        @endcan
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Logo and Basic Info -->
                        <div>
                            @if($plant->logo_path)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $plant->logo_path) }}" alt="{{ $plant->name }} Logo" class="w-24 h-24 rounded-lg border border-slate-200 object-contain bg-slate-50 p-2">
                                </div>
                            @endif
                            
                            <div class="space-y-3">
                                @if($plant->description)
                                    <div>
                                        <label class="text-sm font-medium text-slate-700">Description</label>
                                        <p class="text-slate-600">{{ $plant->description }}</p>
                                    </div>
                                @endif

                                @if($plant->established_date)
                                    <div>
                                        <label class="text-sm font-medium text-slate-700">Established</label>
                                        <p class="text-slate-600">{{ $plant->established_date->format('F j, Y') }}</p>
                                    </div>
                                @endif

                                @if($plant->capacity)
                                    <div>
                                        <label class="text-sm font-medium text-slate-700">Capacity</label>
                                        <p class="text-slate-600">{{ number_format($plant->capacity) }} units</p>
                                    </div>
                                @endif

                                @if($plant->certification)
                                    <div>
                                        <label class="text-sm font-medium text-slate-700">Certifications</label>
                                        <p class="text-slate-600">{{ $plant->certification }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="space-y-3">
                            <h3 class="font-medium text-slate-800 border-b border-slate-200 pb-2">Contact Information</h3>
                            
                            @if($plant->address)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Address</label>
                                    <p class="text-slate-600">{{ $plant->address }}</p>
                                </div>
                            @endif

                            @if($plant->phone)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Phone</label>
                                    <p class="text-slate-600">{{ $plant->phone }}</p>
                                </div>
                            @endif

                            @if($plant->email)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Email</label>
                                    <p class="text-slate-600">{{ $plant->email }}</p>
                                </div>
                            @endif

                            @if($plant->operating_hours)
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Operating Hours</label>
                                    <p class="text-slate-600">{{ $plant->formatted_operating_hours }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Manager Information -->
                    @if($plant->manager_name)
                        <div class="mt-6 pt-6 border-t border-slate-200">
                            <h3 class="font-medium text-slate-800 mb-3">Plant Manager</h3>
                            <div class="bg-slate-50 p-4 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-slate-700">Name</label>
                                        <p class="text-slate-600">{{ $plant->manager_name }}</p>
                                    </div>
                                    @if($plant->manager_email)
                                        <div>
                                            <label class="text-sm font-medium text-slate-700">Email</label>
                                            <p class="text-slate-600">{{ $plant->manager_email }}</p>
                                        </div>
                                    @endif
                                    @if($plant->manager_phone)
                                        <div>
                                            <label class="text-sm font-medium text-slate-700">Phone</label>
                                            <p class="text-slate-600">{{ $plant->manager_phone }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Recent Activities -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Recent Document Activities</h2>
                    <div class="space-y-3">
                        @forelse($recentDocuments as $document)
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-slate-800">{{ $document->documentType->name }}</p>
                                    <p class="text-sm text-slate-600">{{ $document->employee->first_name }} {{ $document->employee->last_name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-slate-500">{{ $document->created_at->diffForHumans() }}</p>
                                    <p class="text-xs text-slate-400">Expires: {{ $document->expiry_date->format('M j, Y') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-slate-500 py-4">No recent document activities.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Document Status Overview -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Document Status Overview</h2>
                    
                    <!-- Expiring Documents -->
                    @if($expiringDocuments->count() > 0)
                        <div class="mb-6">
                            <h3 class="font-medium text-yellow-800 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Expiring Soon ({{ $expiringDocuments->count() }})
                            </h3>
                            <div class="space-y-2">
                                @foreach($expiringDocuments->take(5) as $document)
                                    <div class="flex items-center justify-between p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <div>
                                            <p class="font-medium text-slate-800">{{ $document->documentType->name }}</p>
                                            <p class="text-sm text-slate-600">{{ $document->employee->first_name }} {{ $document->employee->last_name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-yellow-800">{{ $document->expiry_date->format('M j, Y') }}</p>
                                            <p class="text-xs text-yellow-600">{{ $document->expiry_date->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Expired Documents -->
                    @if($expiredDocuments->count() > 0)
                        <div>
                            <h3 class="font-medium text-red-800 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Expired ({{ $expiredDocuments->count() }})
                            </h3>
                            <div class="space-y-2">
                                @foreach($expiredDocuments->take(5) as $document)
                                    <div class="flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-lg">
                                        <div>
                                            <p class="font-medium text-slate-800">{{ $document->documentType->name }}</p>
                                            <p class="text-sm text-slate-600">{{ $document->employee->first_name }} {{ $document->employee->last_name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-red-800">{{ $document->expiry_date->format('M j, Y') }}</p>
                                            <p class="text-xs text-red-600">{{ $document->expiry_date->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($expiringDocuments->count() === 0 && $expiredDocuments->count() === 0)
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-slate-900">All documents are up to date</h3>
                            <p class="mt-1 text-sm text-slate-500">No expired or expiring documents found.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Statistics and Quick Actions -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 gap-4">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-600">Total Employees</p>
                                <p class="text-3xl font-bold text-indigo-600">{{ $plant->employees->count() }}</p>
                            </div>
                            <div class="p-3 bg-indigo-100 rounded-lg">
                                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-600">Active Projects</p>
                                <p class="text-3xl font-bold text-green-600">{{ $plant->projects->count() }}</p>
                            </div>
                            <div class="p-3 bg-green-100 rounded-lg">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-600">Workflows</p>
                                <p class="text-3xl font-bold text-purple-600">{{ $plant->workflows->count() }}</p>
                            </div>
                            <div class="p-3 bg-purple-100 rounded-lg">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('employees.index', ['plant_id' => $plant->id]) }}" class="block w-full text-left px-4 py-3 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                <span class="font-medium text-indigo-700">View Employees</span>
                            </div>
                        </a>

                        <a href="{{ route('projects.index') }}" class="block w-full text-left px-4 py-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <span class="font-medium text-green-700">View Projects</span>
                            </div>
                        </a>

                        <a href="{{ route('reports.expiring-documents.form', ['plant_id' => $plant->id]) }}" class="block w-full text-left px-4 py-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="font-medium text-yellow-700">Generate Reports</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Location Map (if coordinates available) -->
                @if($plant->latitude && $plant->longitude)
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4">Location</h3>
                        <div class="aspect-video bg-slate-100 rounded-lg flex items-center justify-center">
                            <div class="text-center">
                                <svg class="mx-auto h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <p class="text-sm text-slate-500 mt-2">Map integration available</p>
                                <p class="text-xs text-slate-400">{{ $plant->latitude }}, {{ $plant->longitude }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>