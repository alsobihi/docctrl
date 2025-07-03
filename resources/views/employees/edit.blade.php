<x-app-layout>
    <div class="p-6 lg:p-8" x-data="employeeForm()">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('employees.index') }}" class="text-slate-600 hover:text-slate-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-slate-900">Edit Employee: {{ $employee->full_name }}</h1>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-4xl mx-auto">
            <form action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="first_name" :value="__('First Name')" />
                            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name', $employee->first_name)" required autofocus />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="last_name" :value="__('Last Name')" />
                            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name', $employee->last_name)" required />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="employee_code" :value="__('Employee Code')" />
                            <x-text-input id="employee_code" class="block mt-1 w-full" type="text" name="employee_code" :value="old('employee_code', $employee->employee_code)" required />
                            <x-input-error :messages="$errors->get('employee_code')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="badge_number" :value="__('Badge Number')" />
                            <x-text-input id="badge_number" class="block mt-1 w-full" type="text" name="badge_number" :value="old('badge_number', $employee->badge_number)" />
                            <x-input-error :messages="$errors->get('badge_number')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="plant_id" :value="__('Plant')" />
                            <select name="plant_id" id="plant_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select a Plant</option>
                                @foreach ($plants as $plant)
                                    <option value="{{ $plant->id }}" {{ old('plant_id', $employee->plant_id) == $plant->id ? 'selected' : '' }}>
                                        {{ $plant->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('plant_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="profile_photo" :value="__('Profile Photo')" />
                            @if($employee->profile_photo_path)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $employee->profile_photo_path) }}" alt="Current Photo" class="w-16 h-16 rounded-full border border-slate-200 object-cover">
                                    <p class="text-xs text-slate-500">Current photo</p>
                                </div>
                            @endif
                            <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <p class="text-xs text-slate-500 mt-1">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</p>
                            <x-input-error :messages="$errors->get('profile_photo')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Contact Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="phone" :value="__('Phone Number')" />
                            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone', $employee->phone)" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $employee->email)" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-input-label for="address" :value="__('Address')" />
                        <textarea name="address" id="address" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address', $employee->address) }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Personal Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                            <x-text-input id="date_of_birth" class="block mt-1 w-full" type="date" name="date_of_birth" :value="old('date_of_birth', $employee->date_of_birth?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="nationality" :value="__('Nationality')" />
                            <x-text-input id="nationality" class="block mt-1 w-full" type="text" name="nationality" :value="old('nationality', $employee->nationality)" />
                            <x-input-error :messages="$errors->get('nationality')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Employment Information -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Employment Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="position" :value="__('Position')" />
                            <x-text-input id="position" class="block mt-1 w-full" type="text" name="position" :value="old('position', $employee->position)" />
                            <x-input-error :messages="$errors->get('position')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="department" :value="__('Department')" />
                            <x-text-input id="department" class="block mt-1 w-full" type="text" name="department" :value="old('department', $employee->department)" />
                            <x-input-error :messages="$errors->get('department')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="hire_date" :value="__('Hire Date')" />
                            <x-text-input id="hire_date" class="block mt-1 w-full" type="date" name="hire_date" :value="old('hire_date', $employee->hire_date?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('hire_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="employment_status" :value="__('Employment Status')" />
                            <select name="employment_status" id="employment_status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="active" @selected(old('employment_status', $employee->employment_status) == 'active')>Active</option>
                                <option value="inactive" @selected(old('employment_status', $employee->employment_status) == 'inactive')>Inactive</option>
                                <option value="terminated" @selected(old('employment_status', $employee->employment_status) == 'terminated')>Terminated</option>
                            </select>
                            <x-input-error :messages="$errors->get('employment_status')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="contract_type" :value="__('Contract Type')" />
                            <select name="contract_type" id="contract_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select Contract Type</option>
                                <option value="permanent" @selected(old('contract_type', $employee->contract_type) == 'permanent')>Permanent</option>
                                <option value="contract" @selected(old('contract_type', $employee->contract_type) == 'contract')>Contract</option>
                                <option value="temporary" @selected(old('contract_type', $employee->contract_type) == 'temporary')>Temporary</option>
                            </select>
                            <x-input-error :messages="$errors->get('contract_type')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="contract_end_date" :value="__('Contract End Date')" />
                            <x-text-input id="contract_end_date" class="block mt-1 w-full" type="date" name="contract_end_date" :value="old('contract_end_date', $employee->contract_end_date?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('contract_end_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="salary" :value="__('Salary')" />
                            <x-text-input id="salary" class="block mt-1 w-full" type="number" step="0.01" name="salary" :value="old('salary', $employee->salary)" />
                            <x-input-error :messages="$errors->get('salary')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Emergency Contact</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="emergency_contact_name" :value="__('Contact Name')" />
                            <x-text-input id="emergency_contact_name" class="block mt-1 w-full" type="text" name="emergency_contact_name" :value="old('emergency_contact_name', $employee->emergency_contact_name)" />
                            <x-input-error :messages="$errors->get('emergency_contact_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="emergency_contact_phone" :value="__('Contact Phone')" />
                            <x-text-input id="emergency_contact_phone" class="block mt-1 w-full" type="tel" name="emergency_contact_phone" :value="old('emergency_contact_phone', $employee->emergency_contact_phone)" />
                            <x-input-error :messages="$errors->get('emergency_contact_phone')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="emergency_contact_relationship" :value="__('Relationship')" />
                            <x-text-input id="emergency_contact_relationship" class="block mt-1 w-full" type="text" name="emergency_contact_relationship" :value="old('emergency_contact_relationship', $employee->emergency_contact_relationship)" />
                            <x-input-error :messages="$errors->get('emergency_contact_relationship')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Skills and Notes -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Additional Information</h2>
                    
                    <div class="mb-6">
                        <x-input-label for="skills" :value="__('Skills')" />
                        <div class="mt-2">
                            <div class="flex flex-wrap gap-2 mb-2" x-show="skills.length > 0">
                                <template x-for="(skill, index) in skills" :key="index">
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm rounded-full flex items-center gap-2">
                                        <span x-text="skill"></span>
                                        <button type="button" @click="removeSkill(index)" class="text-indigo-600 hover:text-indigo-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                        <input type="hidden" :name="'skills[' + index + ']'" :value="skill">
                                    </span>
                                </template>
                            </div>
                            <div class="flex gap-2">
                                <x-text-input x-model="newSkill" @keydown.enter.prevent="addSkill()" class="flex-1" type="text" placeholder="Add a skill..." />
                                <button type="button" @click="addSkill()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Add</button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <x-input-label for="notes" :value="__('Notes')" />
                        <textarea name="notes" id="notes" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $employee->notes) }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('employees.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <x-primary-button>
                        {{ __('Update Employee') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function employeeForm() {
            return {
                skills: @json(old('skills', $employee->skills ?? [])),
                newSkill: '',
                addSkill() {
                    if (this.newSkill.trim() && !this.skills.includes(this.newSkill.trim())) {
                        this.skills.push(this.newSkill.trim());
                        this.newSkill = '';
                    }
                },
                removeSkill(index) {
                    this.skills.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>