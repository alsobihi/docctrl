<x-app-layout>
    <div class="p-6 lg:p-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <h1 class="text-3xl font-bold text-slate-900">Employee Management</h1>
            <a href="{{ route('employees.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 flex items-center gap-2 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Add New Employee</span>
            </a>
        </div>

        <!-- Advanced Search and Filter Form -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mb-8">
            <form action="{{ route('employees.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div class="md:col-span-2">
                        <x-input-label for="search" :value="__('Search')" />
                        <x-text-input id="search" class="block mt-1 w-full" type="text" name="search" :value="request('search')" placeholder="Name, code, position, department..." />
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
                    <div>
                        <x-input-label for="department" :value="__('Department')" />
                        <select name="department" id="department" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department }}" @selected(request('department') == $department)>{{ $department }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="employment_status" :value="__('Status')" />
                        <select name="employment_status" id="employment_status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">All Status</option>
                            <option value="active" @selected(request('employment_status') == 'active')>Active</option>
                            <option value="inactive" @selected(request('employment_status') == 'inactive')>Inactive</option>
                            <option value="terminated" @selected(request('employment_status') == 'terminated')>Terminated</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <x-primary-button class="w-full md:w-auto justify-center">
                            {{ __('Filter') }}
                        </x-primary-button>
                        <a href="{{ route('employees.index') }}" class="ml-2 px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Employee Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($employees as $employee)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <!-- Employee Header with Photo -->
                    <div class="relative h-32 bg-gradient-to-br from-indigo-500 to-purple-600">
                        <div class="absolute bottom-4 left-4">
                            @if($employee->profile_photo_path)
                                <img src="{{ asset('storage/' . $employee->profile_photo_path) }}" alt="{{ $employee->full_name }}" class="w-16 h-16 rounded-full border-4 border-white shadow-lg object-cover">
                            @else
                                <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center border-4 border-white shadow-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $employee->employment_status === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($employee->employment_status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($employee->employment_status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Employee Content -->
                    <div class="p-6">
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-slate-900 mb-1">{{ $employee->full_name }}</h3>
                            <p class="text-sm text-slate-500">{{ $employee->employee_code }}</p>
                            @if($employee->position)
                                <p class="text-sm font-medium text-indigo-600">{{ $employee->position }}</p>
                            @endif
                            @if($employee->department)
                                <p class="text-xs text-slate-500">{{ $employee->department }}</p>
                            @endif
                        </div>

                        <!-- Employee Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center">
                                <div class="text-lg font-bold text-red-600">{{ $employee->expired_documents_count }}</div>
                                <div class="text-xs text-slate-500">Expired Docs</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-yellow-600">{{ $employee->expiring_documents_count }}</div>
                                <div class="text-xs text-slate-500">Expiring Soon</div>
                            </div>
                        </div>

                        <!-- Plant Info -->
                        @if($employee->plant)
                            <div class="mb-4 p-3 bg-slate-50 rounded-lg">
                                <div class="text-sm font-medium text-slate-700">Plant</div>
                                <div class="text-sm text-slate-600">{{ $employee->plant->name }}</div>
                            </div>
                        @endif

                        <!-- Contact Info -->
                        @if($employee->phone || $employee->email)
                            <div class="mb-4 space-y-1">
                                @if($employee->phone)
                                    <div class="flex items-center gap-2 text-sm text-slate-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        {{ $employee->phone }}
                                    </div>
                                @endif
                                @if($employee->email)
                                    <div class="flex items-center gap-2 text-sm text-slate-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $employee->email }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                            <a href="{{ route('employees.show', $employee) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                View Profile
                            </a>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('employees.documents.index', $employee) }}" class="text-green-600 hover:text-green-900 text-sm">
                                    Documents
                                </a>
                                <a href="{{ route('employees.edit', $employee) }}" class="text-slate-600 hover:text-slate-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this employee?');">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-slate-900">No employees found</h3>
                        <p class="mt-1 text-sm text-slate-500">Try adjusting your search or filter criteria.</p>
                        <div class="mt-6">
                            <a href="{{ route('employees.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add New Employee
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($employees->hasPages())
            <div class="mt-8">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</x-app-layout>