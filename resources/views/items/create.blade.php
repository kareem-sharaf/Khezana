<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('items.actions.create') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Category -->
                        <div class="mb-4">
                            <x-input-label for="category_id" :value="__('items.fields.category')" />
                            <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="">{{ __('items.placeholders.category_id') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <!-- Operation Type -->
                        <div class="mb-4">
                            <x-input-label for="operation_type" :value="__('items.fields.operation_type')" />
                            <select name="operation_type" id="operation_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="sell" {{ old('operation_type') == 'sell' ? 'selected' : '' }}>{{ __('items.operation_types.sell') }}</option>
                                <option value="rent" {{ old('operation_type') == 'rent' ? 'selected' : '' }}>{{ __('items.operation_types.rent') }}</option>
                                <option value="donate" {{ old('operation_type') == 'donate' ? 'selected' : '' }}>{{ __('items.operation_types.donate') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('operation_type')" class="mt-2" />
                        </div>

                        <!-- Title -->
                        <div class="mb-4">
                            <x-input-label for="title" :value="__('items.fields.title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('items.fields.description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" rows="4">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Price (conditional) -->
                        <div class="mb-4" id="price-field">
                            <x-input-label for="price" :value="__('items.fields.price')" />
                            <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1 block w-full" :value="old('price')" />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- Deposit Amount (conditional) -->
                        <div class="mb-4" id="deposit-field" style="display: none;">
                            <x-input-label for="deposit_amount" :value="__('items.fields.deposit_amount')" />
                            <x-text-input id="deposit_amount" name="deposit_amount" type="number" step="0.01" class="mt-1 block w-full" :value="old('deposit_amount')" />
                            <x-input-error :messages="$errors->get('deposit_amount')" class="mt-2" />
                        </div>

                        <!-- Dynamic Attributes will be loaded via JavaScript based on category -->

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('items.actions.create') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show/hide price and deposit fields based on operation type
        document.getElementById('operation_type').addEventListener('change', function() {
            const operationType = this.value;
            const priceField = document.getElementById('price-field');
            const depositField = document.getElementById('deposit-field');
            const priceInput = document.getElementById('price');
            const depositInput = document.getElementById('deposit_amount');

            if (operationType === 'sell' || operationType === 'rent') {
                priceField.style.display = 'block';
                priceInput.required = true;
            } else {
                priceField.style.display = 'none';
                priceInput.required = false;
            }

            if (operationType === 'rent') {
                depositField.style.display = 'block';
                depositInput.required = true;
            } else {
                depositField.style.display = 'none';
                depositInput.required = false;
            }
        });

        // Trigger on page load
        document.getElementById('operation_type').dispatchEvent(new Event('change'));
    </script>
</x-app-layout>
