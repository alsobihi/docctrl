<x-app-layout>
    <div class="p-6 lg:p-8">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('workflows.edit', $workflow) }}" class="text-slate-600 hover:text-slate-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Workflow History</h1>
                <p class="text-slate-500 mt-1">{{ $workflow->name }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-slate-800">Activity Timeline</h2>
                <div class="flex items-center gap-2">
                    <select id="filter-action" class="border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="">All Actions</option>
                        <option value="started">Started</option>
                        <option value="completed">Completed</option>
                        <option value="reopened">Reopened</option>
                        <option value="document_added">Document Added</option>
                        <option value="document_expired">Document Expired</option>
                        <option value="document_deleted">Document Deleted</option>
                    </select>
                    <select id="filter-employee" class="border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="">All Employees</option>
                        @foreach($workflow->history->pluck('employee')->unique('id') as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="relative">
                <!-- Timeline Line -->
                <div class="absolute left-9 top-0 bottom-0 w-0.5 bg-slate-200"></div>

                <!-- Timeline Events -->
                <div class="space-y-8">
                    @forelse($workflow->history->sortByDesc('created_at') as $event)
                        <div class="relative flex items-start">
                            <!-- Timeline Icon -->
                            <div class="absolute left-0 mt-1.5">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center 
                                    {{ $event->action === 'started' ? 'bg-blue-100' : 
                                       ($event->action === 'completed' ? 'bg-green-100' : 
                                       ($event->action === 'reopened' ? 'bg-yellow-100' : 
                                       ($event->action === 'document_added' ? 'bg-indigo-100' : 
                                       ($event->action === 'document_expired' || $event->action === 'document_deleted' ? 'bg-red-100' : 'bg-slate-100')))) }}">
                                    
                                    @if($event->action === 'started')
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif($event->action === 'completed')
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif($event->action === 'reopened')
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    @elseif($event->action === 'document_added')
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    @elseif($event->action === 'document_expired')
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif($event->action === 'document_deleted')
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <!-- Event Content -->
                            <div class="ml-14">
                                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <span class="font-medium text-slate-800">{{ $event->employee->full_name }}</span>
                                            <span class="mx-2 text-slate-400">•</span>
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                {{ $event->action === 'started' ? 'bg-blue-100 text-blue-800' : 
                                                ($event->action === 'completed' ? 'bg-green-100 text-green-800' : 
                                                ($event->action === 'reopened' ? 'bg-yellow-100 text-yellow-800' : 
                                                ($event->action === 'document_added' ? 'bg-indigo-100 text-indigo-800' : 
                                                ($event->action === 'document_expired' || $event->action === 'document_deleted' ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-800')))) }}">
                                                {{ ucfirst(str_replace('_', ' ', $event->action)) }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-slate-500">{{ $event->created_at->format('M j, Y g:i A') }}</span>
                                    </div>
                                    
                                    <p class="text-sm text-slate-600">{{ $event->details }}</p>
                                    
                                    @if($event->documentType)
                                        <div class="mt-2 p-2 bg-white rounded border border-slate-200">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-slate-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span class="text-xs font-medium text-slate-700">{{ $event->documentType->name }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($event->createdBy)
                                        <div class="mt-2 text-xs text-slate-500">
                                            By: {{ $event->createdBy->name }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-500">
                            <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-slate-900">No history found</h3>
                            <p class="mt-1 text-sm text-slate-500">This workflow doesn't have any recorded activity yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterAction = document.getElementById('filter-action');
            const filterEmployee = document.getElementById('filter-employee');
            const timelineEvents = document.querySelectorAll('.timeline-event');
            
            function applyFilters() {
                const actionFilter = filterAction.value;
                const employeeFilter = filterEmployee.value;
                
                timelineEvents.forEach(event => {
                    const eventAction = event.getAttribute('data-action');
                    const eventEmployee = event.getAttribute('data-employee');
                    
                    const actionMatch = !actionFilter || eventAction === actionFilter;
                    const employeeMatch = !employeeFilter || eventEmployee === employeeFilter;
                    
                    if (actionMatch && employeeMatch) {
                        event.style.display = '';
                    } else {
                        event.style.display = 'none';
                    }
                });
            }
            
            filterAction.addEventListener('change', applyFilters);
            filterEmployee.addEventListener('change', applyFilters);
        });
    </script>
</x-app-layout>