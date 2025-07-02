<x-app-layout>
    <div class="p-6 lg:p-8" x-data="{ scope: 'global' }">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Add New Workflow</h1>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-2xl mx-auto">
            <form action="{{ route('workflows.store') }}" method="POST">
                @csrf
                <div>
                    <x-input-label for="name" :value="__('Workflow Name')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                </div>
                <div class="mt-4">
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea name="description" id="description" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description') }}</textarea>
                </div>

                <!-- Scope Selection -->
                <div class="mt-4">
                    <x-input-label :value="__('Scope')" />
                    <div class="flex items-center space-x-4 mt-2">
                        <label class="flex items-center"><input type="radio" name="scope" value="global" x-model="scope" class="form-radio"> <span class="ml-2">Global</span></label>
                        <label class="flex items-center"><input type="radio" name="scope" value="plant" x-model="scope" class="form-radio"> <span class="ml-2">Plant Specific</span></label>
                        <label class="flex items-center"><input type="radio" name="scope" value="project" x-model="scope" class="form-radio"> <span class="ml-2">Project Specific</span></label>
                    </div>
                </div>

                <!-- Plant Dropdown -->
                <div x-show="scope === 'plant'" x-transition class="mt-4">
                    <x-input-label for="plant_id" :value="__('Select Plant')" />
                    <select name="plant_id" id="plant_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        @foreach($plants as $plant) <option value="{{ $plant->id }}">{{ $plant->name }}</option> @endforeach
                    </select>
                </div>

                <!-- Project Dropdown -->
                <div x-show="scope === 'project'" x-transition class="mt-4">
                    <x-input-label for="project_id" :value="__('Select Project')" />
                    <select name="project_id" id="project_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        @foreach($projects as $project) <option value="{{ $project->id }}">{{ $project->name }}</option> @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('workflows.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <x-primary-button>{{ __('Save and Add Steps') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
