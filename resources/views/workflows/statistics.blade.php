<x-app-layout>
    <div class="p-6 lg:p-8">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('workflows.edit', $workflow) }}" class="text-slate-600 hover:text-slate-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Workflow Statistics</h1>
                <p class="text-slate-500 mt-1">{{ $workflow->name }}</p>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600">Total Assignments</p>
                        <p class="text-3xl font-bold text-indigo-600">{{ $totalAssignments }}</p>
                    </div>
                    <div class="p-3 bg-indigo-100 rounded-lg">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600">Completed</p>
                        <p class="text-3xl font-bold text-green-600">{{ $completedAssignments }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600">In Progress</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $inProgressAssignments }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600">Completion Rate</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $completionRate }}%</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Document Compliance -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <h2 class="text-xl font-semibold text-slate-800 mb-6">Document Compliance</h2>
                
                <div class="space-y-4">
                    @foreach($documentCompliance as $compliance)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-slate-700">{{ $compliance['document_type']->name }}</span>
                                <span class="text-sm font-medium text-slate-700">{{ $compliance['present_count'] }}/{{ $compliance['required_count'] }}</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $compliance['compliance_rate'] }}%"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-xs text-slate-500">{{ $compliance['compliance_rate'] }}% compliance</span>
                                <span class="text-xs text-slate-500">
                                    @if($compliance['compliance_rate'] < 50)
                                        Needs attention
                                    @elseif($compliance['compliance_rate'] < 80)
                                        Improving
                                    @else
                                        Good compliance
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Reopened Workflows -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <h2 class="text-xl font-semibold text-slate-800 mb-6">Reopened Workflows</h2>
                
                @if($reopenedWorkflows->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($reopenedWorkflows as $reopened)
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-slate-800">{{ $reopened->employee->full_name }}</span>
                                    <span class="text-xs text-slate-500">{{ $reopened->reopened_at->format('M j, Y') }}</span>
                                </div>
                                <p class="text-sm text-slate-600">{{ $reopened->reopened_reason }}</p>
                                <div class="mt-2 flex justify-between items-center">
                                    <span class="text-xs text-slate-500">{{ $reopened->days_since_reopened }} days since reopened</span>
                                    <a href="{{ route('process-workflow.show', ['employee' => $reopened->employee_id, 'workflow' => $workflow->id]) }}" class="text-xs text-indigo-600 hover:text-indigo-900">View Checklist</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-slate-500">
                        <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-slate-900">No reopened workflows</h3>
                        <p class="mt-1 text-sm text-slate-500">All workflows are progressing normally.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- All Assignments Table -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mt-8">
            <h2 class="text-xl font-semibold text-slate-800 mb-6">All Assignments</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Started</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Completed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Progress</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($workflow->assignments()->with('employee')->latest()->get() as $assignment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($assignment->employee->profile_photo_path)
                                            <img src="{{ asset('storage/' . $assignment->employee->profile_photo_path) }}" alt="{{ $assignment->employee->full_name }}" class="w-8 h-8 rounded-full mr-3">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-slate-900">{{ $assignment->employee->full_name }}</div>
                                            <div class="text-xs text-slate-500">{{ $assignment->employee->employee_code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $assignment->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($assignment->status) }}
                                    </span>
                                    @if($assignment->reopened_at)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 ml-1">
                                            Reopened
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $assignment->created_at->format('M j, Y') }}
                                    <div class="text-xs text-slate-400">{{ $assignment->days_since_start }} days ago</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    @if($assignment->completed_at)
                                        {{ $assignment->completed_at->format('M j, Y') }}
                                        <div class="text-xs text-slate-400">{{ $assignment->days_since_completion }} days ago</div>
                                    @else
                                        <span class="text-xs text-slate-400">Not completed</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-slate-200 rounded-full h-2 mr-2">
                                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $assignment->completion_percentage }}%"></div>
                                        </div>
                                        <span class="text-xs text-slate-500">{{ $assignment->completion_percentage }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('process-workflow.show', ['employee' => $assignment->employee_id, 'workflow' => $workflow->id]) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-slate-500">No assignments found for this workflow.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>