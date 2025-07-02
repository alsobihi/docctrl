<x-app-layout>
    <div class="p-6 lg:p-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Workflow Management</h1>
            <a href="{{ route('workflows.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Add New Workflow</span>
            </a>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Scope</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Steps</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($workflows as $workflow)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ $workflow->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ Str::limit($workflow->description, 50) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    @if($workflow->plant)
                                        <span class="font-semibold">Plant:</span> {{ $workflow->plant->name }}
                                    @elseif($workflow->project)
                                        <span class="font-semibold">Project:</span> {{ $workflow->project->name }}
                                    @else
                                        <span class="font-semibold text-indigo-600">Global</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $workflow->document_types_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('workflows.edit', $workflow) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    {{-- ... delete form ... --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">No workflows found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $workflows->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
