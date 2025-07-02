<x-app-layout>
    <div class="p-6 lg:p-8">
        <!-- Profile Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">{{ $employee->first_name }} {{ $employee->last_name }}</h1>
                <p class="text-slate-500 mt-1">
                    <span class="font-semibold">{{ $employee->employee_code }}</span> | Belongs to: <span class="font-semibold">{{ $employee->plant->name ?? 'N/A' }}</span>
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('employees.documents.create', $employee) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <span>Add Document</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Documents & Workflows -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Documents Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h2 class="text-xl font-semibold mb-4 text-slate-800">Documents</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Document Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Expiry Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase"></th>
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

                 <!-- In Progress Workflows Card -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h2 class="text-xl font-semibold mb-4 text-slate-800">In Progress Workflows</h2>
                    <div class="space-y-3">
                        @forelse ($employee->assignedWorkflows->where('status', 'in_progress') as $item)
                             <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                                <span class="font-medium text-slate-700">{{ $item->workflow->name }}</span>
                                <a href="{{ route('process-workflow.show', ['employee' => $employee, 'workflow' => $item->workflow]) }}" class="text-sm text-indigo-600 hover:text-indigo-800">View Checklist</a>
                            </div>
                        @empty
                            <p class="text-center text-slate-500 py-4">No workflows currently in progress.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column: Projects -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <h2 class="text-xl font-semibold mb-4 text-slate-800">Assigned Projects</h2>
                    <div class="space-y-3">
                        @forelse ($employee->projects as $project)
                            <div class="p-3 bg-slate-50 rounded-lg">
                                <p class="font-semibold text-slate-800">{{ $project->name }}</p>
                                <p class="text-sm text-slate-500">{{ $project->project_code }}</p>
                            </div>
                        @empty
                            <p class="text-center text-slate-500 py-4">Not assigned to any projects.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
