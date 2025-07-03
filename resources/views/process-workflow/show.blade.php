<x-app-layout>
    <div class="p-6 lg:p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">{{ $workflow->name }}</h1>
            <p class="text-slate-500 mt-1">Checklist for: <span class="font-semibold">{{ $employee->first_name }} {{ $employee->last_name }}</span></p>
        </div>

        <!-- Completion Banner -->
        @if ($employeeWorkflow->status === 'completed')
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-8" role="alert">
                <p class="font-bold">Workflow Completed</p>
                <p>All required documents have been collected for this workflow. (Completed on: {{ $employeeWorkflow->completed_at->format('d M Y') }})</p>
            </div>
        @endif

        <!-- Reopened Banner -->
        @if ($employeeWorkflow->reopened_at)
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-8" role="alert">
                <p class="font-bold">Workflow Reopened</p>
                <p>This workflow was reopened on {{ $employeeWorkflow->reopened_at->format('d M Y') }}.</p>
                @if ($employeeWorkflow->reopened_reason)
                    <p>Reason: {{ $employeeWorkflow->reopened_reason }}</p>
                @endif
            </div>
        @endif

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-slate-800">Required Documents</h2>
                <div class="flex items-center gap-2">
                    <div class="text-sm text-slate-500">Progress:</div>
                    <div class="w-32 bg-slate-200 rounded-full h-2.5">
                        <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $employeeWorkflow->completion_percentage }}%"></div>
                    </div>
                    <div class="text-sm font-medium text-slate-700">{{ $employeeWorkflow->completion_percentage }}%</div>
                </div>
            </div>
            
            <div class="space-y-4">
                @foreach ($checklist as $item)
                    <div class="flex justify-between items-center p-4 rounded-lg {{ $item->is_complete ? 'bg-green-50 border-green-200' : 'bg-slate-50 border-slate-200' }} border">
                        <div class="flex items-center gap-4">
                            @if ($item->is_complete)
                                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-full bg-slate-300 flex items-center justify-center text-slate-600 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-slate-800">{{ $item->name }}</p>
                                <p class="text-sm text-slate-500">{{ $item->category }}</p>
                            </div>
                        </div>

                        <div>
                            @if ($item->is_complete)
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Completed
                                </span>
                            @else
                                <a href="{{ route('employees.documents.create', ['employee' => $employee, 'document_type_id' => $item->document_type_id, 'workflow_id' => $workflow->id]) }}" class="bg-indigo-600 text-white px-4 py-2 text-sm font-medium rounded-lg shadow hover:bg-indigo-700">
                                    Add Document
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-6 text-right">
                <a href="{{ route('process-workflow.create') }}" class="text-sm text-gray-600 hover:text-gray-900">Start a different workflow</a>
            </div>
        </div>

        <!-- Workflow History -->
        @if(isset($employeeWorkflow->history) && $employeeWorkflow->history->count() > 0)
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mt-8">
                <h2 class="text-xl font-semibold text-slate-800 mb-6">Workflow History</h2>
                
                <div class="relative">
                    <!-- Timeline Line -->
                    <div class="absolute left-9 top-0 bottom-0 w-0.5 bg-slate-200"></div>
                    
                    <!-- Timeline Events -->
                    <div class="space-y-6">
                        @foreach($employeeWorkflow->history->sortByDesc('created_at') as $event)
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
                                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-200">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-sm font-medium text-slate-800">{{ ucfirst(str_replace('_', ' ', $event->action)) }}</span>
                                            <span class="text-xs text-slate-500">{{ $event->created_at->format('M j, Y g:i A') }}</span>
                                        </div>
                                        <p class="text-sm text-slate-600">{{ $event->details }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>