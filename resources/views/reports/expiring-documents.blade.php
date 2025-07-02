<x-app-layout>
    <div class="p-6 lg:p-8">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Expiring Documents Report</h1>

        <!-- Filter Form -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mb-8">
            <form action="{{ route('reports.expiring-documents.generate') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <x-input-label for="start_date" :value="__('Start Date')" />
                        <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="request('start_date')" required />
                    </div>
                    <div>
                        <x-input-label for="end_date" :value="__('End Date')" />
                        <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="request('end_date')" required />
                    </div>
                    <div>
                        <x-input-label for="plant_id" :value="__('Plant (Optional)')" />
                        <select name="plant_id" id="plant_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">All Plants</option>
                            @foreach($plants as $plant)
                                <option value="{{ $plant->id }}" @selected(request('plant_id') == $plant->id)>{{ $plant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end mt-6">
                    <x-primary-button>
                        {{ __('Generate Report') }}
                    </x-primary-button>
                </div>
            </form>
        </div>

        <!-- Report Results -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h2 class="text-xl font-semibold mb-4 text-slate-800">Report Results</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Plant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Document</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Expiry Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($documents as $document)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ $document->employee->first_name }} {{ $document->employee->last_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $document->employee->plant->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $document->documentType->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">{{ $document->expiry_date->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                    <p>Please select your filters and click "Generate Report" to see the results.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
