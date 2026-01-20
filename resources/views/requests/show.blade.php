<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $requestModel->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        @if(auth()->id() === $requestModel->user_id)
                            @if(!$requestModel->isClosed() && !$requestModel->isFulfilled())
                                <a href="{{ route('requests.edit', $requestModel) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                                    {{ __('requests.actions.edit') }}
                                </a>
                                @if(!$requestModel->isPending() && !$requestModel->isApproved())
                                    <form method="POST" action="{{ route('requests.submit-for-approval', $requestModel) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">
                                            {{ __('requests.actions.submit_for_approval') }}
                                        </button>
                                    </form>
                                @endif
                                @if($requestModel->isOpen())
                                    <form method="POST" action="{{ route('requests.close', $requestModel) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                            {{ __('requests.actions.close') }}
                                        </button>
                                    </form>
                                @endif
                            @endif
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-bold mb-2">{{ __('requests.fields.title') }}</h3>
                            <p>{{ $requestModel->title }}</p>

                            <h3 class="text-lg font-bold mb-2 mt-4">{{ __('requests.fields.description') }}</h3>
                            <p>{{ $requestModel->description ?? __('requests.messages.no_description') }}</p>

                            <h3 class="text-lg font-bold mb-2 mt-4">{{ __('requests.fields.category') }}</h3>
                            <p>{{ $requestModel->category->name }}</p>

                            <h3 class="text-lg font-bold mb-2 mt-4">{{ __('requests.fields.status') }}</h3>
                            <p>
                                <span class="px-2 py-1 rounded text-sm {{ $requestModel->status->color() === 'success' ? 'bg-green-100 text-green-800' : ($requestModel->status->color() === 'gray' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $requestModel->status->label() }}
                                </span>
                            </p>
                        </div>

                        <div>
                            @if($requestModel->itemAttributes->count() > 0)
                                <h3 class="text-lg font-bold mb-2">{{ __('requests.fields.attributes') }}</h3>
                                <dl class="grid grid-cols-2 gap-2">
                                    @foreach($requestModel->itemAttributes as $itemAttribute)
                                        <dt class="font-semibold">{{ $itemAttribute->attribute->name }}:</dt>
                                        <dd>{{ $itemAttribute->value }}</dd>
                                    @endforeach
                                </dl>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        @if($requestModel->isPending())
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">{{ __('requests.messages.pending_approval') }}</span>
                        @elseif($requestModel->isApproved())
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">{{ __('requests.messages.approved') }}</span>
                        @elseif($requestModel->isRejected())
                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">{{ __('requests.messages.rejected') }}</span>
                        @endif
                    </div>

                    <!-- Offers Section -->
                    @if($requestModel->isApproved() && $requestModel->isOpen())
                        <div class="mt-8 border-t pt-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold">{{ __('offers.title') }}</h3>
                                @if(auth()->id() !== $requestModel->user_id)
                                    @php
                                        $userHasOffer = $requestModel->offers->contains('user_id', auth()->id());
                                    @endphp
                                    @if(!$userHasOffer)
                                        <a href="{{ route('offers.create', $requestModel) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            {{ __('offers.actions.create') }}
                                        </a>
                                    @endif
                                @endif
                            </div>

                            @if($requestModel->offers->count() > 0)
                                <div class="space-y-4">
                                    @foreach($requestModel->offers as $offer)
                                        <div class="border rounded-lg p-4 {{ $offer->isAccepted() ? 'bg-green-50 border-green-200' : '' }}">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <span class="font-semibold">{{ $offer->user->name }}</span>
                                                        <span class="px-2 py-1 rounded text-xs {{ $offer->status->color() === 'success' ? 'bg-green-100 text-green-800' : ($offer->status->color() === 'warning' ? 'bg-yellow-100 text-yellow-800' : ($offer->status->color() === 'danger' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                                            {{ $offer->status->label() }}
                                                        </span>
                                                        <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">
                                                            {{ __('items.operation_types.' . $offer->operation_type->value) }}
                                                        </span>
                                                    </div>
                                                    
                                                    @if($offer->item)
                                                        <p class="text-sm text-gray-600 mb-2">
                                                            {{ __('offers.fields.linked_item') }}: 
                                                            <a href="{{ route('items.show', $offer->item) }}" class="text-blue-600 hover:underline">
                                                                {{ $offer->item->title }}
                                                            </a>
                                                        </p>
                                                    @endif

                                                    @if($offer->price)
                                                        <p class="text-sm font-semibold mb-1">
                                                            {{ __('offers.fields.price') }}: {{ number_format($offer->price, 2) }} SYP
                                                        </p>
                                                    @endif

                                                    @if($offer->deposit_amount)
                                                        <p class="text-sm text-gray-600 mb-1">
                                                            {{ __('offers.fields.deposit_amount') }}: {{ number_format($offer->deposit_amount, 2) }} SYP
                                                        </p>
                                                    @endif

                                                    @if($offer->message)
                                                        <p class="text-sm text-gray-700 mt-2">{{ $offer->message }}</p>
                                                    @endif
                                                </div>

                                                <div class="flex gap-2">
                                                    @if(auth()->id() === $requestModel->user_id && $offer->isPending())
                                                        <form method="POST" action="{{ route('offers.accept', $offer) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white text-sm font-bold py-1 px-3 rounded">
                                                                {{ __('offers.actions.accept') }}
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('offers.reject', $offer) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white text-sm font-bold py-1 px-3 rounded">
                                                                {{ __('offers.actions.reject') }}
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if(auth()->id() === $offer->user_id && $offer->isPending())
                                                        <a href="{{ route('offers.edit', $offer) }}" class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-bold py-1 px-3 rounded">
                                                            {{ __('offers.actions.edit') }}
                                                        </a>
                                                        <form method="POST" action="{{ route('offers.cancel', $offer) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white text-sm font-bold py-1 px-3 rounded">
                                                                {{ __('offers.actions.cancel') }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">{{ __('offers.messages.no_offers') }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
