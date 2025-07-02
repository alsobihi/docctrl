<x-app-layout>
    <div class="p-6 lg:p-8">
        <h1 class="text-3xl font-bold text-slate-900 mb-8">Edit Project</h1>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-2xl mx-auto">
            <form action="{{ route('projects.update', $project) }}" method="POST">
                @csrf
                @method('PUT')
                <div>
                    <x-input-label for="name" :value="__('Project Name')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $project->name)" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="project_code" :value="__('Project Code')" />
                    <x-text-input id="project_code" class="block mt-1 w-full" type="text" name="project_code" :value="old('project_code', $project->project_code)" required />
                    <x-input-error :messages="$errors->get('project_code')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('projects.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <x-primary-button>
                        {{ __('Update Project') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
