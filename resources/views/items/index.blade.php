<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('items.title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('items.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('items.actions.create') }}
                        </a>
                    </div>

                    @if ($items->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($items as $item)
                                <div class="border rounded-lg p-4">
                                    <h3 class="font-bold text-lg mb-2">
                                        <a href="{{ route('items.show', $item) }}"
                                            class="text-blue-600 hover:underline">
                                            {{ $item->title }}
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 text-sm mb-2">{{ Str::limit($item->description, 100) }}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">{{ $item->category->name }}</span>
                                        @if ($item->price)
                                            <span class="font-bold">{{ number_format($item->price, 2) }} SYP</span>
                                        @endif
                                    </div>
                                    <div class="mt-2">
                                        @if ($item->isPending())
                                            <span
                                                class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">{{ __('items.messages.submitted_for_approval') }}</span>
                                        @elseif($item->isApproved())
                                            <span
                                                class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">{{ __('Approved') }}</span>
                                        @elseif($item->isRejected())
                                            <span
                                                class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">{{ __('Rejected') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $items->links() }}
                        </div>
                    @else
                        <p class="text-gray-500">{{ __('No items found.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
