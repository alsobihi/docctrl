<x-app-layout>
    <div class="p-6 lg:p-8" x-data="{ rule_type: '{{ old('rule_type', $documentType->validity_rule['type'] ?? 'none') }}' }">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Edit Document Type</h1>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-2xl mx-auto">
            <form action="{{ route('document-types.update', $documentType) }}" method="POST">
                @csrf
                @method('PUT')
                <div>
                    <x-input-label for="name" :value="__('Document Name')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $documentType->name)" required autofocus />
                </div>
                <div class="mt-4">
                    <x-input-label for="category" :value="__('Category')" />
                    <select name="category" id="category" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="Personal" @selected(old('category', $documentType->category) == 'Personal')>Personal</option>
                        <option value="Project" @selected(old('category', $documentType->category) == 'Project')>Project</option>
                        <option value="Plant" @selected(old('category', $documentType->category) == 'Plant')>Plant</option>
                    </select>
                </div>
                <div class="mt-4">
                    <x-input-label for="template_id" :value="__('Document Template (Optional)')" />
                    <select name="template_id" id="template_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">None</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}" @selected(old('template_id', $documentType->template_id) == $template->id)>{{ $template->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-4">
                    <x-input-label for="warning_period_days" :value="__('Warning Period (Days)')" />
                    <x-text-input id="warning_period_days" class="block mt-1 w-full" type="number" name="warning_period_days" :value="old('warning_period_days', $documentType->warning_period_days)" />
                </div>
                <div class="mt-6 border-t pt-6">
                    <h3 class="text-lg font-semibold text-slate-800">Validity Rule</h3>
                    <div class="mt-2">
                        <div class="flex items-center space-x-4 mt-2">
                            <label class="flex items-center"><input type="radio" name="rule_type" value="none" x-model="rule_type" class="form-radio"> <span class="ml-2">None (Manual Expiry)</span></label>
                            <label class="flex items-center"><input type="radio" name="rule_type" value="fixed" x-model="rule_type" class="form-radio"> <span class="ml-2">Fixed Duration</span></label>
                            <label class="flex items-center"><input type="radio" name="rule_type" value="dependent" x-model="rule_type" class="form-radio"> <span class="ml-2">Dependent</span></label>
                        </div>
                    </div>
                    <div x-show="rule_type === 'fixed'" x-transition class="mt-4">
                        <x-input-label for="fixed_days" :value="__('Expires After (Days)')" />
                        <x-text-input id="fixed_days" class="block mt-1 w-full" type="number" name="fixed_days" :value="old('fixed_days', $documentType->validity_rule['days'] ?? '')" />
                    </div>
                    <div x-show="rule_type === 'dependent'" x-transition class="mt-4">
                        <x-input-label for="dependencies" :value="__('Depends On')" />
                        @php
                            $selectedDependencies = collect(old('dependencies', $documentType->validity_rule['dependencies'] ?? []))->pluck('document_type_id')->all();
                        @endphp
                        <select name="dependencies[]" id="dependencies" multiple class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            @foreach ($dependencyTypes as $type)
                                <option value="{{ $type->id }}" @selected(in_array($type->id, $selectedDependencies))>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('document-types.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <x-primary-button>{{ __('Update Document Type') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
