<x-app-layout>
    <div class="p-6 lg:p-8">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('plants.index') }}" class="text-slate-600 hover:text-slate-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-slate-900">Add New Plant</h1>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-4xl mx-auto">
            <form action="{{ route('plants.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Basic Information -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Plant Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="location" :value="__('Location')" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location')" />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select name="status" id="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="active" @selected(old('status') == 'active')>Active</option>
                                <option value="inactive" @selected(old('status') == 'inactive')>Inactive</option>
                                <option value="maintenance" @selected(old('status') == 'maintenance')>Maintenance</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="logo" :value="__('Plant Logo')" />
                            <input type="file" name="logo" id="logo" accept="image/*" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <p class="text-xs text-slate-500 mt-1">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</p>
                            <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea name="description" id="description" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Contact Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="phone" :value="__('Phone Number')" />
                            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-input-label for="address" :value="__('Address')" />
                        <textarea name="address" id="address" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address') }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>
                </div>

                <!-- Manager Information -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Plant Manager</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="manager_name" :value="__('Manager Name')" />
                            <x-text-input id="manager_name" class="block mt-1 w-full" type="text" name="manager_name" :value="old('manager_name')" />
                            <x-input-error :messages="$errors->get('manager_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="manager_email" :value="__('Manager Email')" />
                            <x-text-input id="manager_email" class="block mt-1 w-full" type="email" name="manager_email" :value="old('manager_email')" />
                            <x-input-error :messages="$errors->get('manager_email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="manager_phone" :value="__('Manager Phone')" />
                            <x-text-input id="manager_phone" class="block mt-1 w-full" type="tel" name="manager_phone" :value="old('manager_phone')" />
                            <x-input-error :messages="$errors->get('manager_phone')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Additional Details -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Additional Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="established_date" :value="__('Established Date')" />
                            <x-text-input id="established_date" class="block mt-1 w-full" type="date" name="established_date" :value="old('established_date')" />
                            <x-input-error :messages="$errors->get('established_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="capacity" :value="__('Capacity (units)')" />
                            <x-text-input id="capacity" class="block mt-1 w-full" type="number" name="capacity" :value="old('capacity')" min="1" />
                            <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="certification" :value="__('Certifications')" />
                            <x-text-input id="certification" class="block mt-1 w-full" type="text" name="certification" :value="old('certification')" placeholder="e.g., ISO 9001, ISO 14001" />
                            <x-input-error :messages="$errors->get('certification')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label :value="__('Operating Hours')" />
                            <div class="grid grid-cols-2 gap-2 mt-1">
                                <x-text-input type="time" name="operating_hours_start" :value="old('operating_hours_start')" placeholder="Start time" />
                                <x-text-input type="time" name="operating_hours_end" :value="old('operating_hours_end')" placeholder="End time" />
                            </div>
                            <x-input-error :messages="$errors->get('operating_hours_start')" class="mt-2" />
                            <x-input-error :messages="$errors->get('operating_hours_end')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Location Coordinates -->
                <div class="mb-8 border-t border-slate-200 pt-8">
                    <h2 class="text-xl font-semibold text-slate-800 mb-4">Location Coordinates (Optional)</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="latitude" :value="__('Latitude')" />
                            <x-text-input id="latitude" class="block mt-1 w-full" type="number" step="any" name="latitude" :value="old('latitude')" placeholder="e.g., 25.2048" />
                            <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="longitude" :value="__('Longitude')" />
                            <x-text-input id="longitude" class="block mt-1 w-full" type="number" step="any" name="longitude" :value="old('longitude')" placeholder="e.g., 55.2708" />
                            <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('plants.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <x-primary-button>
                        {{ __('Save Plant') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>