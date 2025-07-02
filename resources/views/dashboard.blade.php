<x-app-layout>
    <div class="p-6 lg:p-8">
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>
                <p class="text-slate-500 mt-1">Overview of compliance status for: <span class="font-semibold text-indigo-600">All Plants</span></p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('process-workflow.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 flex items-center gap-2 transition-all duration-200 hover:shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 lucide lucide-play-circle"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
                    <span class="font-semibold">Start Workflow</span>
                </a>
            </div>
        </header>

        <!-- KPIs -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Active Employees Card (Not clickable) -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-start justify-between">
                <div><h3 class="text-sm font-medium text-slate-500 uppercase tracking-wider">Active Employees</h3><p class="text-4xl font-bold mt-2 text-slate-900">{{ $totalEmployees }}</p></div>
                <div class="bg-blue-100 p-3 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users text-blue-600"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
            </div>

            <!-- Workflows in Progress Card (Not clickable) -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex items-start justify-between">
                 <div><h3 class="text-sm font-medium text-slate-500 uppercase tracking-wider">Workflows In Progress</h3><p class="text-4xl font-bold mt-2 text-slate-900">{{ $workflowsInProgress }}</p></div>
                <div class="bg-purple-100 p-3 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-git-branch-plus text-purple-600"><path d="M6 3v12"/><path d="M18 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M6 21a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M15 6a9 9 0 0 0-9 9"/><path d="M18 15v6"/><path d="M21 18h-6"/></svg></div>
            </div>

            <!-- Expiring Soon Card (Clickable) -->
            <a href="{{ route('reports.expiring-documents.generate', ['start_date' => now()->toDateString(), 'end_date' => now()->addDays(30)->toDateString()]) }}"
               class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-yellow-400 flex items-start justify-between hover:bg-yellow-50 transition-colors">
                <div>
                    <h3 class="text-sm font-medium text-yellow-600 uppercase tracking-wider">Expiring Soon</h3>
                    <p class="text-4xl font-bold mt-2 text-yellow-700">{{ $expiringSoonCount }}</p>
                    <p class="text-xs text-slate-500 mt-1">In next 30 days</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-alarm-clock text-yellow-600"><circle cx="12" cy="13" r="8"/><path d="M12 9v4l2 2"/><path d="M5 3 2 6"/><path d="m22 6-3-3"/></svg></div>
            </a>

            <!-- Expired Docs Card (Clickable) -->
            <a href="{{ route('reports.expiring-documents.generate', ['start_date' => now()->subYears(10)->toDateString(), 'end_date' => now()->subDay()->toDateString()]) }}"
               class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-red-500 flex items-start justify-between hover:bg-red-50 transition-colors">
                <div>
                    <h3 class="text-sm font-medium text-red-600 uppercase tracking-wider">Expired Docs</h3>
                    <p class="text-4xl font-bold mt-2 text-red-700">{{ $expiredCount }}</p>
                    <p class="text-xs text-slate-500 mt-1">Action required</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-alert text-red-600"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg></div>
            </a>
        </div>

        <!-- Urgent Renewals Table -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h2 class="text-xl font-semibold mb-4 text-slate-800">Urgent Document Renewals</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Document</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Expiry Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($urgentDocuments as $document)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-slate-900">{{ $document->employee->first_name }} {{ $document->employee->last_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $document->employee->employee_code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $document->documentType->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $document->expiry_date->format('d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($document->expiry_date->isPast())
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Expired
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Expiring Soon
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-12 h-12 mx-auto text-slate-300 lucide lucide-inbox"><path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
                                    <p class="mt-4 font-medium">No urgent documents found.</p>
                                    <p class="text-sm text-slate-400">Everything is up-to-date.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
