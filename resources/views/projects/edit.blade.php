<x-app-layout>
    <div class="p-6 lg:p-8" x-data="projectForm()">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('projects.index') }}" class="text-slate-600 hover:text-slate-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-slate-900">Edit Project: {{ $project->name }}</h1>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-4xl mx-auto">
            <form action="{{ route('projects.update', $project) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Project Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $project->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="project_code" :value="__('Project Code')" />
                            <x-text-input id="project_code" class="block mt-1 w-full" type="text" name="project_code" :value="old('project_code', $project->project_code)" required />
                            <x-input-error :messages="$errors->get('project_code')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="plant_id" :value="__('Plant')" />
                            <select name="plant_id" id="plant_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select a Plant</option>
                                @foreach ($plants as $plant)
                                    <option value="{{ $plant->id }}" {{ old('plant_id', $project->plant_id) == $plant->id ? 'selected' : '' }}>
                                        {{ $plant->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('plant_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="location" :value="__('Location')" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location', $project->location)" />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select name="status" id="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="planning" @selected(old('status', $project->status) == 'planning')>Planning</option>
                                <option value="active" @selected(old('status', $project->status) == 'active')>Active</option>
                                <option value="on_hold" @selected(old('status', $project->status) == 'on_hold')>On Hold</option>
                                <option value="completed" @selected(old('status', $project->status) == 'completed')>Completed</option>
                                <option value="cancelled" @selected(old('status', $project->status) == 'cancelled')>Cancelled</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="priority" :value="__('Priority')" />
                            <select name="priority" id="priority" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="low" @selected(old('priority', $project->priority) == 'low')>Low</option>
                                <option value="medium" @selected(old('priority', $project->priority) == 'medium')>Medium</option>
                                <option value="high" @selected(old('priority', $project->priority) == 'high')>High</option>
                                <option value="critical" @selected(old('priority', $project->priority) == 'critical')>Critical</option>
                            </select>
                            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea name="description" id="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $project->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                </div>

                <!-- Client Information -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Client Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="client_name" :value="__('Client Name')" />
                            <x-text-input id="client_name" class="block mt-1 w-full" type="text" name="client_name" :value="old('client_name', $project->client_name)" />
                            <x-input-error :messages="$errors->get('client_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="client_contact" :value="__('Client Contact')" />
                            <x-text-input id="client_contact" class="block mt-1 w-full" type="text" name="client_contact" :value="old('client_contact', $project->client_contact)" />
                            <x-input-error :messages="$errors->get('client_contact')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="client_email" :value="__('Client Email')" />
                            <x-text-input id="client_email" class="block mt-1 w-full" type="email" name="client_email" :value="old('client_email', $project->client_email)" />
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
                            <x-text-input id="project_manager" class="block mt-1 w-full" type="text" name="project_manager" :value="old('project_manager', $project->project_manager)" />
                            <x-input-error :messages="$errors->get('project_manager')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="project_manager_email" :value="__('Manager Email')" />
                            <x-text-input id="project_manager_email" class="block mt-1 w-full" type="email" name="project_manager_email" :value="old('project_manager_email', $project->project_manager_email)" />
                            <x-input-error :messages="$errors->get('project_manager_email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="project_manager_phone" :value="__('Manager Phone')" />
                            <x-text-input id="project_manager_phone" class="block mt-1 w-full" type="tel" name="project_manager_phone" :value="old('project_manager_phone', $project->project_manager_phone)" />
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
                            <x-text-input id="budget" class="block mt-1 w-full" type="number" step="0.01" name="budget" :value="old('budget', $project->budget)" />
                            <x-input-error :messages="$errors->get('budget')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="actual_cost" :value="__('Actual Cost')" />
                            <x-text-input id="actual_cost" class="block mt-1 w-full" type="number" step="0.01" name="actual_cost" :value="old('actual_cost', $project->actual_cost)" />
                            <x-input-error :messages="$errors->get('actual_cost')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="start_date" :value="__('Planned Start Date')" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date', $project->start_date?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="end_date" :value="__('Planned End Date')" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date', $project->end_date?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="actual_start_date" :value="__('Actual Start Date')" />
                            <x-text-input id="actual_start_date" class="block mt-1 w-full" type="date" name="actual_start_date" :value="old('actual_start_date', $project->actual_start_date?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('actual_start_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="actual_end_date" :value="__('Actual End Date')" />
                            <x-text-input id="actual_end_date" class="block mt-1 w-full" type="date" name="actual_end_date" :value="old('actual_end_date', $project->actual_end_date?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('actual_end_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="progress_percentage" :value="__('Progress (%)')" />
                            <x-text-input id="progress_percentage" class="block mt-1 w-full" type="number" min="0" max="100" name="progress_percentage" :value="old('progress_percentage', $project->progress_percentage)" />
                            <x-input-error :messages="$errors->get('progress_percentage')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Contract Information -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Contract Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="contract_number" :value="__('Contract Number')" />
                            <x-text-input id="contract_number" class="block mt-1 w-full" type="text" name="contract_number" :value="old('contract_number', $project->contract_number)" />
                            <x-input-error :messages="$errors->get('contract_number')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="contract_date" :value="__('Contract Date')" />
                            <x-text-input id="contract_date" class="block mt-1 w-full" type="date" name="contract_date" :value="old('contract_date', $project->contract_date?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('contract_date')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Milestones -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Milestones</h2>
                    <div class="space-y-4">
                        <div class="space-y-3" x-show="milestones.length > 0">
                            <template x-for="(milestone, index) in milestones" :key="index">
                                <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-lg border">
                                    <div class="flex-1">
                                        <x-input-label :value="__('Milestone Name')" />
                                        <x-text-input class="block mt-1 w-full" type="text" x-model="milestone.name" />
                                        <input type="hidden" :name="'milestones[' + index + '][name]'" x-model="milestone.name">
                                    </div>
                                    <div class="w-32">
                                        <x-input-label :value="__('Date')" />
                                        <x-text-input class="block mt-1 w-full" type="date" x-model="milestone.date" />
                                        <input type="hidden" :name="'milestones[' + index + '][date]'" x-model="milestone.date">
                                    </div>
                                    <div class="w-24 pt-6">
                                        <label class="flex items-center">
                                            <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" x-model="milestone.completed">
                                            <span class="ml-2 text-sm text-gray-600">Completed</span>
                                            <input type="hidden" :name="'milestones[' + index + '][completed]'" :value="milestone.completed ? 1 : 0">
                                        </label>
                                    </div>
                                    <div class="pt-6">
                                        <button type="button" @click="removeMilestone(index)" class="text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div x-show="milestones.length === 0" class="text-center py-4 text-slate-500">
                            No milestones added yet.
                        </div>
                        <div class="flex justify-end">
                            <button type="button" @click="addMilestone()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Add Milestone
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Risks -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Risk Assessment</h2>
                    <div class="space-y-4">
                        <div class="space-y-3" x-show="risks.length > 0">
                            <template x-for="(risk, index) in risks" :key="index">
                                <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-lg border">
                                    <div class="flex-1">
                                        <x-input-label :value="__('Risk Name')" />
                                        <x-text-input class="block mt-1 w-full" type="text" x-model="risk.name" />
                                        <input type="hidden" :name="'risks[' + index + '][name]'" x-model="risk.name">
                                    </div>
                                    <div class="w-32">
                                        <x-input-label :value="__('Severity')" />
                                        <select class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" x-model="risk.severity">
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                        </select>
                                        <input type="hidden" :name="'risks[' + index + '][severity]'" x-model="risk.severity">
                                    </div>
                                    <div class="flex-1">
                                        <x-input-label :value="__('Mitigation')" />
                                        <x-text-input class="block mt-1 w-full" type="text" x-model="risk.mitigation" />
                                        <input type="hidden" :name="'risks[' + index + '][mitigation]'" x-model="risk.mitigation">
                                    </div>
                                    <div class="pt-6">
                                        <button type="button" @click="removeRisk(index)" class="text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div x-show="risks.length === 0" class="text-center py-4 text-slate-500">
                            No risks added yet.
                        </div>
                        <div class="flex justify-end">
                            <button type="button" @click="addRisk()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Add Risk
                            </button>
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
                        <textarea name="notes" id="notes" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $project->notes) }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('projects.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <x-primary-button>
                        {{ __('Update Project') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function projectForm() {
            return {
                tags: @json(old('tags', $project->tags ?? [])),
                milestones: @json(old('milestones', $project->milestones ?? [])),
                risks: @json(old('risks', $project->risks ?? [])),
                newTag: '',
                addTag() {
                    if (this.newTag.trim() && !this.tags.includes(this.newTag.trim())) {
                        this.tags.push(this.newTag.trim());
                        this.newTag = '';
                    }
                },
                removeTag(index) {
                    this.tags.splice(index, 1);
                },
                addMilestone() {
                    this.milestones.push({
                        name: '',
                        date: '',
                        completed: false
                    });
                },
                removeMilestone(index) {
                    this.milestones.splice(index, 1);
                },
                addRisk() {
                    this.risks.push({
                        name: '',
                        severity: 'medium',
                        mitigation: ''
                    });
                },
                removeRisk(index) {
                    this.risks.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>