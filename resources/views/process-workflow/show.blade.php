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

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h2 class="text-xl font-semibold mb-4 text-slate-800">Required Documents</h2>
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
                                <a href="{{ route('employees.documents.create', ['employee' => $employee, 'document_type_id' => $item->document_type_id]) }}" class="bg-indigo-600 text-white px-4 py-2 text-sm font-medium rounded-lg shadow hover:bg-indigo-700">
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
    </div>
</x-app-layout>
