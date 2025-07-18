<x-app-layout>
    <div class="p-6 lg:p-8">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Start a New Workflow</h1>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-2xl mx-auto"
             x-data="workflowForm()">
            <form action="{{ route('process-workflow.redirect') }}" method="POST">
                @csrf
                <div>
                    <x-input-label for="employee_id" :value="__('Select Employee')" />
                    <select name="employee_id" id="employee_id" x-model="selectedEmployee" @change="fetchWorkflows()"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <option value="">-- Select an Employee --</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4">
                    <x-input-label for="workflow_id" :value="__('Select Workflow')" />
                    <div class="relative">
                        <select name="workflow_id" id="workflow_id" x-ref="workflowSelect"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required x-bind:disabled="isLoading || !selectedEmployee">
                            <option value="">-- Select an employee first --</option>
                            <template x-for="workflow in workflows" :key="workflow.id">
                                <option :value="workflow.id" x-text="workflow.name"></option>
                            </template>
                        </select>
                        <div x-show="isLoading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <x-primary-button x-bind:disabled="!selectedEmployee || isLoading">
                        {{ __('Generate Checklist') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function workflowForm() {
            return {
                selectedEmployee: '',
                workflows: [],
                isLoading: false,
                fetchWorkflows() {
                    if (!this.selectedEmployee) {
                        this.workflows = [];
                        return;
                    }
                    this.isLoading = true;
                    this.workflows = [];
                    let url = `/employees/${this.selectedEmployee}/relevant-workflows`;
                    fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        this.workflows = data;
                        this.isLoading = false;
                    })
                    .catch(() => {
                        this.isLoading = false;
                        alert('An error occurred while fetching workflows.');
                    });
                }
            }
        }
    </script>
</x-app-layout>
