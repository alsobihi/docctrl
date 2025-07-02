<x-app-layout>
    <div class="p-6 lg:p-8" x-data="{ role: '{{ old('role', $user->role) }}' }">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Edit User</h1>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-2xl mx-auto">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                </div>
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                </div>
                <div class="mt-4">
                    <x-input-label for="password" :value="__('New Password (leave blank to keep current)')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
                </div>
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                </div>
                <div class="mt-4">
                    <x-input-label for="role" :value="__('Role')" />
                    <select name="role" id="role" x-model="role" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="viewer" @selected(old('role', $user->role) == 'viewer')>Viewer</option>
                        <option value="manager" @selected(old('role', $user->role) == 'manager')>Manager</option>
                        <option value="admin" @selected(old('role', $user->role) == 'admin')>Admin</option>
                    </select>
                </div>
                <div x-show="role === 'manager'" x-transition class="mt-4">
                    <x-input-label for="plant_id" :value="__('Assign to Plant')" />
                    <select name="plant_id" id="plant_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select a Plant</option>
                        @foreach($plants as $plant)
                            <option value="{{ $plant->id }}" @selected(old('plant_id', $user->plant_id) == $plant->id)>{{ $plant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <x-primary-button>{{ __('Update User') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
