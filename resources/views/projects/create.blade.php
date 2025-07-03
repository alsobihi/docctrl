<x-app-layout>
    <div class="p-6 lg:p-8" x-data="projectForm()">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('projects.index') }}" class="text-slate-600 hover:text-slate-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-slate-900">Add New Project</h1>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-4xl mx-auto">
            <form action="{{ route('projects.store') }}" method="POST">
                @csrf
                
                <!-- Basic Information -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Project Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="project_code" :value="__('Project Code')" />
                            <x-text-input id="project_code" class="block mt-1 w-full" type="text" name="project_code" :value="old('project_code')" required />
                            <x-input-error :messages="$errors->get('project_code')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="plant_id" :value="__('Plant')" />
                            <select name="plant_id" id="plant_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select a Plant</option>
                                @foreach ($plants as $plant)
                                    <option value="{{ $plant->id }}" {{ old('plant_id') == $plant->id ? 'selected' : '' }}>
                                        {{ $plant->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('plant_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="location" :value="__('Location')" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location')" />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select name="status" id="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="planning" @selected(old('status') == 'planning')>Planning</option>
                                <option value="active" @selected(old('status') == 'active')>Active</option>
                                <option value="on_hold" @selected(old('status') == 'on_hold')>On Hold</option>
                                <option value="completed" @selected(old('status') == 'completed')>Completed</option>
                                <option value="cancelled" @selected(old('status') == 'cancelled')>Cancelled</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="priority" :value="__('Priority')" />
                            <select name="priority" id="priority" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="low" @selected(old('priority') == 'low')>Low</option>
                                <option value="medium" @selected(old('priority') == 'medium')>Medium</option>
                                <option value="high" @selected(old('priority') == 'high')>High</option>
                                <option value="critical" @selected(old('priority') == 'critical')>Critical</option>
                            </select>
                            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea name="description" id="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                </div>

                <!-- Client Information -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Client Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="client_name" :value="__('Client Name')" />
                            <x-text-input id="client_name" class="block mt-1 w-full" type="text" name="client_name" :value="old('client_name')" />
                            <x-input-error :messages="$errors->get('client_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="client_contact" :value="__('Client Contact')" />
                            <x-text-input id="client_contact" class="block mt-1 w-full" type="text" name="client_contact" :value="old('client_contact')" />
                            <x-input-error :messages="$errors->get('client_contact')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="client_email" :value="__('Client Email')" />
                            <x-text-input id="client_email" class="block mt-1 w-full" type="email" name="client_email" :value="old('client_email')" />
                            <x-input-error :messages="$errors->get('client_email')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Project Manager -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Project Manager</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="project_manager" :value="__('Manager Name')" />
                            <x-text-input id="project_manager" class="block mt-1 w-full" type="text" name="project_manager" :value="old('project_manager')" />
                            <x-input-error :messages="$errors->get('project_manager')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="project_manager_email" :value="__('Manager Email')" />
                            <x-text-input id="project_manager_email" class="block mt-1 w-full" type="email" name="project_manager_email" :value="old('project_manager_email')" />
                            <x-input-error :messages="$errors->get('project_manager_email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="project_manager_phone" :value="__('Manager Phone')" />
                            <x-text-input id="project_manager_phone" class="block mt-1 w-full" type="tel" name="project_manager_phone" :value="old('project_manager_phone')" />
                            <x-input-error :messages="$errors->get('project_manager_phone')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Budget & Timeline -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Budget & Timeline</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="budget" :value="__('Budget')" />
                            <x-text-input id="budget" class="block mt-1 w-full" type="number" step="0.01" name="budget" :value="old('budget')" />
                            <x-input-error :messages="$errors->get('budget')" class="mt-2" />
                        </div>

                        <div>
                            <!-- Empty for spacing -->
                        </div>

                        <div>
                            <x-input-label for="start_date" :value="__('Start Date')" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date')" />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="end_date" :value="__('End Date')" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date')" />
                            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Contract Information -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Contract Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="contract_number" :value="__('Contract Number')" />
                            <x-text-input id="contract_number" class="block mt-1 w-full" type="text" name="contract_number" :value="old('contract_number')" />
                            <x-input-error :messages="$errors->get('contract_number')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="contract_date" :value="__('Contract Date')" />
                            <x-text-input id="contract_date" class="block mt-1 w-full" type="date" name="contract_date" :value="old('contract_date')" />
                            <x-input-error :messages="$errors->get('contract_date')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Tags and Notes -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Additional Information</h2>
                    
                    <div class="mb-6">
                        <x-input-label for="tags" :value="__('Tags')" />
                        <div class="mt-2">
                            <div class="flex flex-wrap gap-2 mb-2" x-show="tags.length > 0">
                                <template x-for="(tag, index) in tags" :key="index">
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm rounded-full flex items-center gap-2">
                                        <span x-text="tag"></span>
                                        <button type="button" @click="removeTag(index)" class="text-indigo-600 hover:text-indigo-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                        <input type="hidden" :name="'tags[' + index + ']'" :value="tag">
                                    </span>
                                </template>
                            </div>
                            <div class="flex gap-2">
                                <x-text-input x-model="newTag" @keydown.enter.prevent="addTag()" class="flex-1" type="text" placeholder="Add a tag..." />
                                <button type="button" @click="addTag()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Add</button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <x-input-label for="notes" :value="__('Notes')" />
                        <textarea name="notes" id="notes" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('projects.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <x-primary-button>
                        {{ __('Save Project') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function projectForm() {
            return {
                tags: @json(old('tags', [])),
                newTag: '',
                addTag() {
                    if (this.newTag.trim() && !this.tags.includes(this.newTag.trim())) {
                        this.tags.push(this.newTag.trim());
                        this.newTag = '';
                    }
                },
                removeTag(index) {
                    this.tags.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>