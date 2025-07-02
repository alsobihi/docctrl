<x-app-layout>
    <div class="p-6 lg:p-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <h1 class="text-3xl font-bold text-slate-900">Employee Management</h1>
            <a href="{{ route('employees.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 flex items-center gap-2 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Add New Employee</span>
            </a>
        </div>

        <!-- Search and Filter Form -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-8">
            <form action="{{ route('employees.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <x-input-label for="search" :value="__('Search (Name or Code)')" />
                        <x-text-input id="search" class="block mt-1 w-full" type="text" name="search" :value="request('search')" placeholder="e.g., John Doe or EMP001" />
                    </div>
                    <div>
                        <x-input-label for="plant_id" :value="__('Filter by Plant')" />
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

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Employee Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Plant</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($employees as $employee)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                    <a href="{{ route('employees.show', $employee) }}">{{ $employee->first_name }} {{ $employee->last_name }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $employee->employee_code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $employee->plant->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('employees.show', $employee) }}" class="text-blue-600 hover:text-blue-900">View Profile</a>
                                    <a href="{{ route('employees.documents.index', $employee) }}" class="text-green-600 hover:text-green-900">Documents</a>
                                    <a href="{{ route('employees.edit', $employee) }}" class="text-indigo-600 hover:text-indigo-900 ml-4">Edit</a>
                                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                    <p class="font-medium">No employees found.</p>
                                    <p class="text-sm mt-1">Try adjusting your search or filter criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{-- This automatically includes the search and filter query parameters --}}
                {{ $employees->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
