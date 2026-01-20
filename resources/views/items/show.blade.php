<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $item->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        @if(auth()->id() === $item->user_id)
                            <a href="{{ route('items.edit', $item) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                                {{ __('items.actions.edit') }}
                            </a>
                            @if(!$item->isPending() && !$item->isApproved())
                                <form method="POST" action="{{ route('items.submit-for-approval', $item) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        {{ __('items.actions.submit_for_approval') }}
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-bold mb-2">{{ __('items.fields.title') }}</h3>
                            <p>{{ $item->title }}</p>

                            <h3 class="text-lg font-bold mb-2 mt-4">{{ __('items.fields.description') }}</h3>
                            <p>{{ $item->description }}</p>

                            <h3 class="text-lg font-bold mb-2 mt-4">{{ __('items.fields.category') }}</h3>
                            <p>{{ $item->category->name }}</p>

                            <h3 class="text-lg font-bold mb-2 mt-4">{{ __('items.fields.operation_type') }}</h3>
                            <p>{{ __('items.operation_types.' . $item->operation_type->value) }}</p>

                            @if($item->price)
                                <h3 class="text-lg font-bold mb-2 mt-4">{{ __('items.fields.price') }}</h3>
                                <p>{{ number_format($item->price, 2) }} SYP</p>
                            @endif

                            @if($item->deposit_amount)
                                <h3 class="text-lg font-bold mb-2 mt-4">{{ __('items.fields.deposit_amount') }}</h3>
                                <p>{{ number_format($item->deposit_amount, 2) }} SYP</p>
                            @endif
                        </div>

                        <div>
                            @if($item->images->count() > 0)
                                <h3 class="text-lg font-bold mb-2">{{ __('items.fields.images') }}</h3>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($item->images as $image)
                                        <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $item->title }}" class="w-full h-auto rounded">
                                    @endforeach
                                </div>
                            @endif

                            @if($item->itemAttributes->count() > 0)
                                <h3 class="text-lg font-bold mb-2 mt-4">{{ __('Attributes') }}</h3>
                                <dl class="grid grid-cols-2 gap-2">
                                    @foreach($item->itemAttributes as $itemAttribute)
                                        <dt class="font-semibold">{{ $itemAttribute->attribute->name }}:</dt>
                                        <dd>{{ $itemAttribute->value }}</dd>
                                    @endforeach
                                </dl>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        @if($item->isPending())
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">{{ __('Pending Approval') }}</span>
                        @elseif($item->isApproved())
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">{{ __('Approved') }}</span>
                        @elseif($item->isRejected())
                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">{{ __('Rejected') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
