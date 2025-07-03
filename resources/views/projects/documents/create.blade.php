<x-app-layout>
    <div class="p-6 lg:p-8">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('projects.documents.index', $project) }}" class="text-slate-600 hover:text-slate-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Upload Project Document</h1>
                <p class="text-slate-500 mt-1">For: <span class="font-semibold">{{ $project->name }}</span></p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 max-w-2xl mx-auto">
            <form action="{{ route('projects.documents.store', $project) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div>
                    <x-input-label for="title" :value="__('Document Title')" />
                    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="category" :value="__('Category')" />
                    <select name="category" id="category" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <option value="">Select a category</option>
                        <option value="contract" @selected(old('category') == 'contract')>Contract</option>
                        <option value="technical" @selected(old('category') == 'technical')>Technical</option>
                        <option value="report" @selected(old('category') == 'report')>Report</option>
                        <option value="approval" @selected(old('category') == 'approval')>Approval</option>
                        <option value="financial" @selected(old('category') == 'financial')>Financial</option>
                        <option value="design" @selected(old('category') == 'design')>Design</option>
                        <option value="other" @selected(old('category') == 'other')>Other</option>
                    </select>
                    <x-input-error :messages="$errors->get('category')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea name="description" id="description" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="document_date" :value="__('Document Date')" />
                    <x-text-input id="document_date" class="block mt-1 w-full" type="date" name="document_date" :value="old('document_date')" />
                    <x-input-error :messages="$errors->get('document_date')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="file" :value="__('Upload Document')" />
                    <input type="file" name="file" id="file" class="block mt-1 w-full" required>
                    <p class="text-xs text-slate-500 mt-1">Max size: 10MB. Formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG</p>
                    <x-input-error :messages="$errors->get('file')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="version" :value="__('Version')" />
                    <x-text-input id="version" class="block mt-1 w-full" type="text" name="version" :value="old('version', '1.0')" />
                    <x-input-error :messages="$errors->get('version')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <label for="is_confidential" class="flex items-center">
                        <input id="is_confidential" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_confidential" value="1" {{ old('is_confidential') ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-600">This document is confidential</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('projects.documents.index', $project) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                    <x-primary-button>
                        {{ __('Upload Document') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>