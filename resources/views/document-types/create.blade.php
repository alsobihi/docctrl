<x-app-layout>
    <div class="p-6 lg:p-8" x-data="{ rule_type: '{{ old('rule_type', 'none') }}' }">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Add New Document Type</h1>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-2xl mx-auto">
            <form action="{{ route('document-types.store') }}" method="POST">
                @csrf
                <div>
                    <x-input-label for="name" :value="__('Document Name')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="category" :value="__('Category')" />
                    <select name="category" id="category" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="Personal" @selected(old('category') == 'Personal')>Personal</option>
                        <option value="Project" @selected(old('category') == 'Project')>Project</option>
                        <option value="Plant" @selected(old('category') == 'Plant')>Plant</option>
                    </select>
                </div>

                <!-- Validity Rule Builder -->
                <div class="mt-6 border-t border-slate-200 pt-6">
                    <h3 class="text-lg font-semibold text-slate-800">Validity Rule</h3>
                    <div class="mt-2">
                        <x-input-label :value="__('Rule Type')" />
                        <div class="flex items-center space-x-4 mt-2">
                            <label class="flex items-center"><input type="radio" name="rule_type" value="none" x-model="rule_type" class="form-radio"> <span class="ml-2">None (No Expiry)</span></label>
                            <label class="flex items-center"><input type="radio" name="rule_type" value="fixed" x-model="rule_type" class="form-radio"> <span class="ml-2">Fixed Duration</span></label>
                            <label class="flex items-center"><input type="radio" name="rule_type" value="dependent" x-model="rule_type" class="form-radio"> <span class="ml-2">Dependent</span></label>
                        </div>
                    </div>

                    <!-- Fixed Duration Input -->
                    <div x-show="rule_type === 'fixed'" x-transition class="mt-4">
                        <x-input-label for="fixed_days" :value="__('Expires After (Days)')" />
                        <x-text-input id="fixed_days" class="block mt-1 w-full" type="number" name="fixed_days" :value="old('fixed_days')" />
                        <x-input-error :messages="$errors->get('fixed_days')" class="mt-2" />
                    </div>

                    <!-- Dependent Input -->
                    <div x-show="rule_type === 'dependent'" x-transition class="mt-4">
                        <x-input-label for="dependencies" :value="__('Depends On (Select one or more)')" />
                        <select name="dependencies[]" id="dependencies" multiple class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @foreach ($dependencyTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-500">The document will expire when the EARLIEST of the selected documents expires.</p>
                        <x-input-error :messages="$errors->get('dependencies')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('document-types.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <x-primary-button>
                        {{ __('Save Document Type') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
