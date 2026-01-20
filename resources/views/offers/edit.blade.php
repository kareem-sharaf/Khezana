<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('offers.actions.edit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold">{{ __('requests.fields.title') }}: {{ $offer->request->title }}</h3>
                    </div>

                    <form method="POST" action="{{ route('offers.update', $offer) }}">
                        @csrf
                        @method('PUT')

                        <!-- Operation Type -->
                        <div class="mb-4">
                            <x-input-label for="operation_type" :value="__('offers.fields.operation_type')" />
                            <select name="operation_type" id="operation_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="sell" {{ old('operation_type', $offer->operation_type->value) == 'sell' ? 'selected' : '' }}>{{ __('items.operation_types.sell') }}</option>
                                <option value="rent" {{ old('operation_type', $offer->operation_type->value) == 'rent' ? 'selected' : '' }}>{{ __('items.operation_types.rent') }}</option>
                                <option value="donate" {{ old('operation_type', $offer->operation_type->value) == 'donate' ? 'selected' : '' }}>{{ __('items.operation_types.donate') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('operation_type')" class="mt-2" />
                        </div>

                        <!-- Item Selection (Optional) -->
                        <div class="mb-4">
                            <x-input-label for="item_id" :value="__('offers.fields.item')" />
                            <select name="item_id" id="item_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">{{ __('offers.placeholders.no_item') }}</option>
                                @foreach($userItems as $item)
                                    <option value="{{ $item->id }}" {{ old('item_id', $offer->item_id) == $item->id ? 'selected' : '' }}>
                                        {{ $item->title }} - {{ __('items.operation_types.' . $item->operation_type->value) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('item_id')" class="mt-2" />
                        </div>

                        <!-- Price (conditional) -->
                        <div class="mb-4" id="price-field">
                            <x-input-label for="price" :value="__('offers.fields.price')" />
                            <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1 block w-full" :value="old('price', $offer->price)" />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- Deposit Amount (conditional) -->
                        <div class="mb-4" id="deposit-field" style="display: none;">
                            <x-input-label for="deposit_amount" :value="__('offers.fields.deposit_amount')" />
                            <x-text-input id="deposit_amount" name="deposit_amount" type="number" step="0.01" class="mt-1 block w-full" :value="old('deposit_amount', $offer->deposit_amount)" />
                            <x-input-error :messages="$errors->get('deposit_amount')" class="mt-2" />
                        </div>

                        <!-- Message -->
                        <div class="mb-4">
                            <x-input-label for="message" :value="__('offers.fields.message')" />
                            <textarea id="message" name="message" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" rows="4">{{ old('message', $offer->message) }}</textarea>
                            <x-input-error :messages="$errors->get('message')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('offers.actions.update') }}
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

            if (operationType === 'sell') {
                priceField.style.display = 'block';
                depositField.style.display = 'none';
                priceInput.required = true;
                depositInput.required = false;
            } else if (operationType === 'rent') {
                priceField.style.display = 'block';
                depositField.style.display = 'block';
                priceInput.required = true;
                depositInput.required = true;
            } else {
                priceField.style.display = 'none';
                depositField.style.display = 'none';
                priceInput.required = false;
                depositInput.required = false;
            }
        });

        // Trigger on page load
        document.getElementById('operation_type').dispatchEvent(new Event('change'));
    </script>
</x-app-layout>
