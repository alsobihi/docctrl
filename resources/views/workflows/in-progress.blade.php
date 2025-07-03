<x-app-layout>
    <div class="p-6 lg:p-8">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Workflows In Progress</h1>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Workflow</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Started On</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($inProgressWorkflows as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $item->employee->first_name }} {{ $item->employee->last_name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $item->workflow->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $item->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    @if($item->reopened_at)
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Reopened
                                        </span>
                                        <div class="text-xs text-slate-500 mt-1">{{ $item->reopened_at->format('d M Y') }}</div>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            In Progress
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <a href="{{ route('process-workflow.show', ['employee' => $item->employee->id, 'workflow' => $item->workflow->id]) }}" class="text-indigo-600 hover:text-indigo-900">View Checklist</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">No workflows are currently in progress.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $inProgressWorkflows->links() }}</div>
        </div>
    </div>
</x-app-layout>