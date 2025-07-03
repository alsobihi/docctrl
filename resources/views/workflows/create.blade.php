<x-app-layout>
    <div class="p-6 lg:p-8" x-data="{ scope: 'global', formSubmitted: false }">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Add New Workflow</h1>
        
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Please check the form for errors.</span>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-2xl mx-auto">
            <form action="{{ route('workflows.store') }}" method="POST" @submit="formSubmitted = true" id="workflowForm">
                @csrf

                <!-- Workflow Name -->
                <div>
                    <x-input-label for="name" :value="__('Workflow Name')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Description -->
                <div class="mt-4">
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea name="description" id="description" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <!-- Scope Selection -->
                <div class="mt-4">
                    <x-input-label :value="__('Scope')" />
                    <div class="flex items-center space-x-4 mt-2">
                        <label class="flex items-center"><input type="radio" name="scope" value="global" x-model="scope" class="form-radio"> <span class="ml-2">Global</span></label>
                        <label class="flex items-center"><input type="radio" name="scope" value="plant" x-model="scope" class="form-radio"> <span class="ml-2">Plant Specific</span></label>
                        <label class="flex items-center"><input type="radio" name="scope" value="project" x-model="scope" class="form-radio"> <span class="ml-2">Project Specific</span></label>
                    </div>
                    <x-input-error :messages="$errors->get('scope')" class="mt-2" />
                </div>

                <!-- Plant Dropdown -->
                <div x-show="scope === 'plant'" x-transition class="mt-4">
                    <x-input-label for="plant_id" :value="__('Select Plant')" />
                    <select name="plant_id" id="plant_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Select a Plant --</option>
                        @foreach($plants as $plant) 
                            <option value="{{ $plant->id }}" {{ old('plant_id') == $plant->id ? 'selected' : '' }}>
                                {{ $plant->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('plant_id')" class="mt-2" />
                </div>

                <!-- Project Dropdown -->
                <div x-show="scope === 'project'" x-transition class="mt-4">
                    <x-input-label for="project_id" :value="__('Select Project')" />
                    <select name="project_id" id="project_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Select a Project --</option>
                        @foreach($projects as $project) 
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
                </div>

                <!-- Workflow Behavior Settings -->
                <div class="mt-6 border-t border-slate-200 pt-6">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Workflow Behavior</h3>
                    
                    <!-- Is Reopenable Checkbox -->
                    <div class="block mt-4">
                        <label for="is_reopenable" class="inline-flex items-center">
                            <input type="hidden" name="is_reopenable" value="0">
                            <input id="is_reopenable" type="checkbox" name="is_reopenable" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_reopenable', true) ? 'checked' : '' }}>
                            <span class="ms-2 text-sm text-gray-600">{{ __('This workflow can be reopened') }}</span>
                        </label>
                        <x-input-error :messages="$errors->get('is_reopenable')" class="mt-2" />
                    </div>
                    
                    <!-- Auto Reopen on Expiry -->
                    <div class="block mt-4">
                        <label for="auto_reopen_on_expiry" class="inline-flex items-center">
                            <input type="hidden" name="auto_reopen_on_expiry" value="0">
                            <input id="auto_reopen_on_expiry" type="checkbox" name="auto_reopen_on_expiry" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('auto_reopen_on_expiry', false) ? 'checked' : '' }}>
                            <span class="ms-2 text-sm text-gray-600">{{ __('Automatically reopen when a document expires') }}</span>
                        </label>
                        <x-input-error :messages="$errors->get('auto_reopen_on_expiry')" class="mt-2" />
                    </div>
                    
                    <!-- Auto Reopen on Deletion -->
                    <div class="block mt-4">
                        <label for="auto_reopen_on_deletion" class="inline-flex items-center">
                            <input type="hidden" name="auto_reopen_on_deletion" value="0">
                            <input id="auto_reopen_on_deletion" type="checkbox" name="auto_reopen_on_deletion" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('auto_reopen_on_deletion', false) ? 'checked' : '' }}>
                            <span class="ms-2 text-sm text-gray-600">{{ __('Automatically reopen when a document is deleted') }}</span>
                        </label>
                        <x-input-error :messages="$errors->get('auto_reopen_on_deletion')" class="mt-2" />
                    </div>
                    
                    <!-- Notification Days Before -->
                    <div class="mt-4">
                        <x-input-label for="notification_days_before" :value="__('Send notifications before expiry (days)')" />
                        <x-text-input id="notification_days_before" class="block mt-1 w-full" type="number" name="notification_days_before" :value="old('notification_days_before', 30)" min="1" max="90" />
                        <p class="text-xs text-slate-500 mt-1">Set how many days before document expiry to send notifications</p>
                        <x-input-error :messages="$errors->get('notification_days_before')" class="mt-2" />
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('workflows.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" x-bind:disabled="formSubmitted">
                        <span x-show="!formSubmitted">{{ __('Save and Add Steps') }}</span>
                        <span x-show="formSubmitted" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add event listener to ensure form submission works
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('workflowForm');
            
            form.addEventListener('submit', function(e) {
                // Prevent multiple submissions
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton.disabled) {
                    e.preventDefault();
                    return false;
                }
                
                // Disable the button to prevent double submission
                submitButton.disabled = true;
                
                // Add a small delay to ensure the form is submitted
                setTimeout(function() {
                    form.submit();
                }, 100);
            });
        });
    </script>
</x-app-layout>