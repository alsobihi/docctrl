<x-app-layout>
    <div class="p-6 lg:p-8" x-data="documentForm({
        documentTypes: {{ $documentTypes->mapWithKeys(fn($type) => [$type->id => ['rule' => (bool)$type->validity_rule, 'template' => $type->template]]) }},
        initialSelected: '{{ $selectedDocumentTypeId ?? '' }}'
    })">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Add New Document</h1>
        <p class="text-slate-500 mb-8">For: <span class="font-semibold">{{ $employee->first_name }} {{ $employee->last_name }}</span></p>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-2xl mx-auto">
            <form action="{{ route('employees.documents.store', $employee) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="workflow_id" value="{{ request('workflow_id') }}">

                <!-- Document Type Dropdown -->
                <div>
                    <x-input-label for="document_type_id" :value="__('Document Type')" />
                    <select name="document_type_id" id="document_type_id" x-model="selectedType" @change="updateForm()" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select a Document Type</option>
                        @foreach ($documentTypes as $type)
                            <option value="{{ $type->id }}" @selected(old('document_type_id', $selectedDocumentTypeId ?? '') == $type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Issue Date -->
                <div class="mt-4">
                    <x-input-label for="issue_date" :value="__('Issue Date')" />
                    <x-text-input id="issue_date" class="block mt-1 w-full" type="date" name="issue_date" :value="old('issue_date')" required />
                </div>

                <!-- Manual Expiry Date (Conditional) -->
                <div x-show="showExpiryField" x-transition class="mt-4">
                    <x-input-label for="expiry_date" :value="__('Expiry Date')" />
                    <x-text-input id="expiry_date" class="block mt-1 w-full" type="date" name="expiry_date" :value="old('expiry_date')" x-bind:required="showExpiryField" />
                </div>

                <!-- Custom Template Fields (Dynamic) -->
                <div x-show="templateFields.length > 0" class="mt-6 border-t pt-6 space-y-4">
                     <h3 class="text-lg font-semibold text-slate-800">Additional Information</h3>
                    <template x-for="(field, index) in templateFields" :key="index">
                        <div>
                            <label :for="'custom_data_' + field.name" class="block text-sm font-medium text-slate-700" x-text="field.label"></label>
                            <input :type="field.type" :name="'custom_data[' + field.name + ']'" :id="'custom_data_' + field.name" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </template>
                </div>

                <!-- File Upload -->
                <div class="mt-4">
                    <x-input-label for="file" :value="__('Upload Scanned Document')" />
                    <input type="file" name="file" id="file" class="block mt-1 w-full" required>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <x-primary-button>{{ __('Save Document') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function documentForm(config) {
            return {
                selectedType: config.initialSelected,
                showExpiryField: false,
                templateFields: [],
                documentTypes: config.documentTypes,
                init() { this.updateForm(); },
                updateForm() {
                    if (!this.selectedType) {
                        this.showExpiryField = false;
                        this.templateFields = [];
                        return;
                    }
                    const details = this.documentTypes[this.selectedType];
                    this.showExpiryField = !details.rule;
                    this.templateFields = details.template ? details.template.fields : [];
                }
            }
        }
    </script>
</x-app-layout>
