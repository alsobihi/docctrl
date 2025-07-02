<x-app-layout>
    <div class="p-6 lg:p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Manage Project Team</h1>
            <p class="text-slate-500 mt-1">For Project: <span class="font-semibold">{{ $project->name }}</span></p>
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
                                    <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Role')" />
                            <select name="role" id="role" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="member">Member</option>
                                <option value="manager">Manager</option>
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
                    <h2 class="text-xl font-semibold mb-4">Current Team</h2>
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse($project->employees as $employee)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-500 capitalize">{{ $employee->pivot->role }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('projects.team.destroy', ['project' => $project, 'employee' => $employee]) }}" method="POST" onsubmit="return confirm('Remove this member?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-6 py-12 text-center text-slate-500">No team members assigned.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
