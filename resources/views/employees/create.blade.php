<x-app-layout>
    <div class="p-6 lg:p-8">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Add New Employee</h1>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-2xl mx-auto">
            <form action="{{ route('employees.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="first_name" :value="__('First Name')" />
                        <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="last_name" :value="__('Last Name')" />
                        <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-4">
                    <x-input-label for="employee_code" :value="__('Employee Code')" />
                    <x-text-input id="employee_code" class="block mt-1 w-full" type="text" name="employee_code" :value="old('employee_code')" required />
                    <x-input-error :messages="$errors->get('employee_code')" class="mt-2" />
                </div>

                <div class="mt-4">
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

                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('employees.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <x-primary-button>
                        {{ __('Save Employee') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
