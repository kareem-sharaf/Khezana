<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('requests.actions.edit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('requests.update', $requestModel) }}">
                        @csrf
                        @method('PUT')

                        <!-- Category -->
                        <div class="mb-4">
                            <x-input-label for="category_id" :value="__('requests.fields.category')" />
                            <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $requestModel->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <!-- Title -->
                        <div class="mb-4">
                            <x-input-label for="title" :value="__('requests.fields.title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $requestModel->title)" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('requests.fields.description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" rows="4">{{ old('description', $requestModel->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Dynamic Attributes will be loaded via JavaScript based on category -->
                        <div id="attributes-container" class="mb-4">
                            <!-- Attributes will be dynamically loaded here -->
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('requests.actions.update') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load attributes when category is selected
        document.getElementById('category_id').addEventListener('change', function() {
            const categoryId = this.value;
            const container = document.getElementById('attributes-container');
            
            if (!categoryId) {
                container.innerHTML = '';
                return;
            }

            // Fetch category attributes (placeholder - implement actual endpoint)
            container.innerHTML = '<p class="text-gray-500">Loading attributes...</p>';
        });
    </script>
</x-app-layout>
