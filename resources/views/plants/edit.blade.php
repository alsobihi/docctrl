<x-app-layout>
    <div class="p-6 lg:p-8">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Edit Plant</h1>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-2xl mx-auto">
            <form action="{{ route('plants.update', $plant) }}" method="POST">
                @csrf
                @method('PUT')
                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Plant Name')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $plant->name)" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Location -->
                <div class="mt-4">
                    <x-input-label for="location" :value="__('Location')" />
                    <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location', $plant->location)" />
                    <x-input-error :messages="$errors->get('location')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('plants.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <x-primary-button>
                        {{ __('Update Plant') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
