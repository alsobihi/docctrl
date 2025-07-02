<x-app-layout>
    <div class="p-6 lg:p-8" x-data="{ role: 'viewer' }">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Add New User</h1>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-2xl mx-auto">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                </div>
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                </div>
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                </div>
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                </div>
                <div class="mt-4">
                    <x-input-label for="role" :value="__('Role')" />
                    <select name="role" id="role" x-model="role" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="viewer">Viewer</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div x-show="role === 'manager'" x-transition class="mt-4">
                    <x-input-label for="plant_id" :value="__('Assign to Plant')" />
                    <select name="plant_id" id="plant_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select a Plant</option>
                        @foreach($plants as $plant)
                            <option value="{{ $plant->id }}">{{ $plant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <x-primary-button>{{ __('Save User') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
