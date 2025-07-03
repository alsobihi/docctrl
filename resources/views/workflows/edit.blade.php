<x-app-layout>
    <div class="p-6 lg:p-8">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

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
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea name="description" id="description" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $workflow->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Scope Selection -->
                        <div class="mt-4">
                            <x-input-label :value="__('Scope')" />
                            <div class="flex items-center space-x-4 mt-2">
                                <label class="flex items-center"><input type="radio" name="scope" value="global" x-model="scope" class="form-radio"> <span class="ml-2">Global</span></label>
                                <label class="flex items-center"><input type="radio" name="scope" value="plant" x-model="scope" class="form-radio"> <span class="ml-2">Plant</span></label>
                                <label class="flex items-center"><input type="radio" name="scope" value="project" x-model="scope" class="form-radio"> <span class="ml-2">Project</span></label>
                            </div>
                            <x-input-error :messages="$errors->get('scope')" class="mt-2" />
                        </div>

                        <!-- Plant Dropdown -->
                        <div x-show="scope === 'plant'" x-transition class="mt-4">
                            <x-input-label for="plant_id" :value="__('Select Plant')" />
                            <select name="plant_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Select a Plant --</option>
                                @foreach($plants as $plant) 
                                    <option value="{{ $plant->id }}" @selected(old('plant_id', $workflow->plant_id) == $plant->id)>{{ $plant->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('plant_id')" class="mt-2" />
                        </div>

                        <!-- Project Dropdown -->
                        <div x-show="scope === 'project'" x-transition class="mt-4">
                            <x-input-label for="project_id" :value="__('Select Project')" />
                            <select name="project_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Select a Project --</option>
                                @foreach($projects as $project) 
                                    <option value="{{ $project->id }}" @selected(old('project_id', $workflow->project_id) == $project->id)>{{ $project->name }}</option>
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
                                    <input id="is_reopenable" type="checkbox" name="is_reopenable" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_reopenable', $workflow->is_reopenable) ? 'checked' : '' }}>
                                    <span class="ms-2 text-sm text-gray-600">{{ __('This workflow can be reopened') }}</span>
                                </label>
                                <x-input-error :messages="$errors->get('is_reopenable')" class="mt-2" />
                            </div>
                            
                            <!-- Auto Reopen on Expiry -->
                            <div class="block mt-4">
                                <label for="auto_reopen_on_expiry" class="inline-flex items-center">
                                    <input type="hidden" name="auto_reopen_on_expiry" value="0">
                                    <input id="auto_reopen_on_expiry" type="checkbox" name="auto_reopen_on_expiry" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('auto_reopen_on_expiry', $workflow->auto_reopen_on_expiry) ? 'checked' : '' }}>
                                    <span class="ms-2 text-sm text-gray-600">{{ __('Automatically reopen when a document expires') }}</span>
                                </label>
                                <x-input-error :messages="$errors->get('auto_reopen_on_expiry')" class="mt-2" />
                            </div>
                            
                            <!-- Auto Reopen on Deletion -->
                            <div class="block mt-4">
                                <label for="auto_reopen_on_deletion" class="inline-flex items-center">
                                    <input type="hidden" name="auto_reopen_on_deletion" value="0">
                                    <input id="auto_reopen_on_deletion" type="checkbox" name="auto_reopen_on_deletion" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('auto_reopen_on_deletion', $workflow->auto_reopen_on_deletion) ? 'checked' : '' }}>
                                    <span class="ms-2 text-sm text-gray-600">{{ __('Automatically reopen when a document is deleted') }}</span>
                                </label>
                                <x-input-error :messages="$errors->get('auto_reopen_on_deletion')" class="mt-2" />
                            </div>
                            
                            <!-- Notification Days Before -->
                            <div class="mt-4">
                                <x-input-label for="notification_days_before" :value="__('Send notifications before expiry (days)')" />
                                <x-text-input id="notification_days_before" class="block mt-1 w-full" type="number" name="notification_days_before" :value="old('notification_days_before', $workflow->notification_days_before ?? 30)" min="1" max="90" />
                                <p class="text-xs text-slate-500 mt-1">Set how many days before document expiry to send notifications</p>
                                <x-input-error :messages="$errors->get('notification_days_before')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>{{ __('Update Workflow') }}</x-primary-button>
                        </div>
                    </form>
                </div>

                <!-- Quick Links -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mt-6">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Quick Links</h3>
                    <div class="space-y-3">
                        <a href="{{ route('workflows.history', $workflow) }}" class="block w-full text-left px-4 py-3 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium text-indigo-700">View History</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('workflows.statistics', $workflow) }}" class="block w-full text-left px-4 py-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span class="font-medium text-green-700">View Statistics</span>
                            </div>
                        </a>
                    </div>
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

                <!-- Workflow Assignments -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mt-8">
                    <h3 class="text-lg font-semibold mb-4">Current Assignments</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Employee</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Progress</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Last Updated</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @forelse($workflow->assignments()->with('employee')->latest()->take(5)->get() as $assignment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($assignment->employee->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $assignment->employee->profile_photo_path) }}" alt="{{ $assignment->employee->full_name }}" class="w-8 h-8 rounded-full mr-3">
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center mr-3">
                                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="text-sm font-medium text-slate-900">{{ $assignment->employee->full_name }}</div>
                                                    <div class="text-xs text-slate-500">{{ $assignment->employee->employee_code }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $assignment->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ ucfirst($assignment->status) }}
                                            </span>
                                            @if($assignment->reopened_at)
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 ml-1">
                                                    Reopened
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-16 bg-slate-200 rounded-full h-2 mr-2">
                                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $assignment->completion_percentage }}%"></div>
                                                </div>
                                                <span class="text-xs text-slate-500">{{ $assignment->completion_percentage }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                            {{ $assignment->updated_at->diffForHumans() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('process-workflow.show', ['employee' => $assignment->employee_id, 'workflow' => $workflow->id]) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-slate-500">No active assignments for this workflow.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($workflow->assignments()->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="{{ route('workflows.statistics', $workflow) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                View all {{ $workflow->assignments()->count() }} assignments
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>