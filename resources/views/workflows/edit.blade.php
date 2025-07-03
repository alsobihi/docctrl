<x-app-layout>
    <div class="p-6 lg:p-8">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Workflow Details -->
            <div class="lg:col-span-1" x-data="{ scope: '{{ old('scope', $workflow->plant_id ? 'plant' : ($workflow->project_id ? 'project' : 'global')) }}' }">
                <h2 class="text-2xl font-bold text-slate-900 mb-4">Workflow Details</h2>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <form action="{{ route('workflows.update', $workflow) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label for="name" :value="__('Workflow Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $workflow->name)" required />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea name="description" id="description" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $workflow->description) }}</textarea>
                        </div>

                        <!-- Scope Selection -->
                        <div class="mt-4">
                            <x-input-label :value="__('Scope')" />
                            <div class="flex items-center space-x-4 mt-2">
                                <label class="flex items-center"><input type="radio" name="scope" value="global" x-model="scope" class="form-radio"> <span class="ml-2">Global</span></label>
                                <label class="flex items-center"><input type="radio" name="scope" value="plant" x-model="scope" class="form-radio"> <span class="ml-2">Plant</span></label>
                                <label class="flex items-center"><input type="radio" name="scope" value="project" x-model="scope" class="form-radio"> <span class="ml-2">Project</span></label>
                            </div>
                        </div>

                        <!-- Plant Dropdown -->
                        <div x-show="scope === 'plant'" x-transition class="mt-4">
                            <x-input-label for="plant_id" :value="__('Select Plant')" />
                            <select name="plant_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @foreach($plants as $plant) <option value="{{ $plant->id }}" @selected(old('plant_id', $workflow->plant_id) == $plant->id)>{{ $plant->name }}</option> @endforeach
                            </select>
                        </div>

                        <!-- Project Dropdown -->
                        <div x-show="scope === 'project'" x-transition class="mt-4">
                            <x-input-label for="project_id" :value="__('Select Project')" />
                            <select name="project_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @foreach($projects as $project) <option value="{{ $project->id }}" @selected(old('project_id', $workflow->project_id) == $project->id)>{{ $project->name }}</option> @endforeach
                            </select>
                        </div>
<div class="mt-4">
    <label for="is_reopenable" class="flex items-center">
        <x-checkbox id="is_reopenable" name="is_reopenable" :value="1" :checked="old('is_reopenable', $workflow->is_reopenable)" />
        <span class="ms-2 text-sm text-gray-600">{{ __('This workflow can be re-opened automatically') }}</span>
    </label>
</div>
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>{{ __('Update Workflow') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Side: Workflow Steps -->
            <div class="lg:col-span-2">
                <h2 class="text-2xl font-bold text-slate-900 mb-4">Workflow Steps</h2>
                <!-- Add New Step Form -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mb-8">
                    <h3 class="text-lg font-semibold mb-4">Add New Step</h3>
                    <form action="{{ route('workflows.steps.store', $workflow) }}" method="POST">
                        @csrf
                        <div class="flex items-end gap-4">
                            <div class="flex-1">
                                <x-input-label for="document_type_id" :value="__('Document Type')" />
                                <select name="document_type_id" id="document_type_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">Select a document to add...</option>
                                    @foreach($availableDocumentTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <x-primary-button>{{ __('Add Step') }}</x-primary-button>
                        </div>
                    </form>
                </div>

                <!-- Existing Steps List -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                     <h3 class="text-lg font-semibold mb-4">Current Steps</h3>
                     <div class="space-y-3">
                        @forelse($workflow->documentTypes as $step)
                            <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                                <span class="font-medium text-slate-700">{{ $step->name }}</span>
                                <form action="{{ route('workflows.steps.destroy', ['step' => $step->pivot->id]) }}" method="POST" onsubmit="return confirm('Remove this step?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-center text-slate-500 py-4">No steps have been added to this workflow yet.</p>
                        @endforelse
                     </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
