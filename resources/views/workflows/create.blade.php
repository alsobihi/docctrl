<x-app-layout>
    <div class="p-6 lg:p-8" x-data="{ scope: 'global' }">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Add New Workflow</h1>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-2xl mx-auto">
            <form action="{{ route('workflows.store') }}" method="POST">
                @csrf

                <!-- Workflow Name -->
                <div>
                    <x-input-label for="name" :value="__('Workflow Name')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                </div>

                <!-- Description -->
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

                <!-- Workflow Behavior Settings -->
                <div class="mt-6 border-t border-slate-200 pt-6">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Workflow Behavior</h3>
                    
                    <!-- Is Reopenable Checkbox -->
                    <div class="block mt-4">
                        <label for="is_reopenable" class="inline-flex items-center">
                            <x-checkbox id="is_reopenable" name="is_reopenable" :checked="old('is_reopenable', true)" />
                            <span class="ms-2 text-sm text-gray-600">{{ __('This workflow can be reopened') }}</span>
                        </label>
                    </div>
                    
                    <!-- Auto Reopen on Expiry -->
                    <div class="block mt-4">
                        <label for="auto_reopen_on_expiry" class="inline-flex items-center">
                            <x-checkbox id="auto_reopen_on_expiry" name="auto_reopen_on_expiry" :checked="old('auto_reopen_on_expiry', false)" />
                            <span class="ms-2 text-sm text-gray-600">{{ __('Automatically reopen when a document expires') }}</span>
                        </label>
                    </div>
                    
                    <!-- Auto Reopen on Deletion -->
                    <div class="block mt-4">
                        <label for="auto_reopen_on_deletion" class="inline-flex items-center">
                            <x-checkbox id="auto_reopen_on_deletion" name="auto_reopen_on_deletion" :checked="old('auto_reopen_on_deletion', false)" />
                            <span class="ms-2 text-sm text-gray-600">{{ __('Automatically reopen when a document is deleted') }}</span>
                        </label>
                    </div>
                    
                    <!-- Notification Days Before -->
                    <div class="mt-4">
                        <x-input-label for="notification_days_before" :value="__('Send notifications before expiry (days)')" />
                        <x-text-input id="notification_days_before" class="block mt-1 w-full" type="number" name="notification_days_before" :value="old('notification_days_before', 30)" min="1" max="90" />
                        <p class="text-xs text-slate-500 mt-1">Set how many days before document expiry to send notifications</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('workflows.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <x-primary-button>{{ __('Save and Add Steps') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>