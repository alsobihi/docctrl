<x-app-layout>
    <div class="p-6 lg:p-8" x-data="templateForm(@json(old('fields', $template->fields ?? [])))">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Edit Document Template</h1>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-4xl mx-auto">
            <form action="{{ route('document-templates.update', $template) }}" method="POST">
                @csrf
                @method('PUT')
                <div>
                    <x-input-label for="name" :value="__('Template Name')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $template->name)" required autofocus />
                </div>

                <!-- Dynamic Fields Builder -->
                <div class="mt-6 border-t border-slate-200 pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-slate-800">Custom Fields</h3>
                        <button type="button" @click="addField()" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">+ Add Field</button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(field, index) in fields" :key="index">
                            <div class="flex items-end gap-4 p-4 bg-slate-50 rounded-lg border">
                                <div class="flex-1">
                                    <x-input-label ::for="'label_' + index" :value="__('Field Label')" />
                                    <x-text-input ::id="'label_' + index" class="block mt-1 w-full" type="text" x-model="field.label" />
                                </div>
                                <div class="flex-1">
                                    <x-input-label ::for="'name_' + index" :value="__('Field Name (no spaces)')" />
                                    <x-text-input ::id="'name_' + index" class="block mt-1 w-full" type="text" x-model="field.name" />
                                </div>
                                <div>
                                    <x-input-label ::for="'type_' + index" :value="__('Field Type')" />
                                    <select ::id="'type_' + index" x-model="field.type" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="text">Text</option>
                                        <option value="date">Date</option>
                                        <option value="number">Number</option>
                                    </select>
                                </div>
                                <div>
                                    <button type="button" @click="removeField(index)" class="text-red-500 hover:text-red-700 p-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                                <input type="hidden" :name="'fields[' + index + '][label]'" x-model="field.label">
                                <input type="hidden" :name="'fields[' + index + '][name]'" x-model="field.name">
                                <input type="hidden" :name="'fields[' + index + '][type]'" x-model="field.type">
                            </div>
                        </template>
                        <p x-show="fields.length === 0" class="text-center text-slate-500 py-4">No custom fields added yet.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('document-templates.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <x-primary-button>
                        {{ __('Update Template') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function templateForm(initialFields) {
            return {
                fields: initialFields || [],
                addField() {
                    this.fields.push({ label: '', name: '', type: 'text' });
                },
                removeField(index) {
                    this.fields.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>
