<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('requests.title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('requests.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('requests.actions.create') }}
                        </a>
                    </div>

                    @if ($requests->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($requests as $request)
                                <div class="border rounded-lg p-4">
                                    <h3 class="font-bold text-lg mb-2">
                                        <a href="{{ route('requests.show', $request) }}"
                                            class="text-blue-600 hover:underline">
                                            {{ $request->title }}
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 text-sm mb-2">{{ Str::limit($request->description, 100) }}</p>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm text-gray-500">{{ $request->category->name }}</span>
                                        <span class="text-sm font-semibold {{ $request->status->color() === 'success' ? 'text-green-600' : ($request->status->color() === 'gray' ? 'text-gray-600' : 'text-blue-600') }}">
                                            {{ $request->status->label() }}
                                        </span>
                                    </div>
                                    <div class="mt-2">
                                        @if ($request->isPending())
                                            <span
                                                class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">{{ __('requests.messages.submitted_for_approval') }}</span>
                                        @elseif($request->isApproved())
                                            <span
                                                class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">{{ __('Approved') }}</span>
                                        @elseif($request->isRejected())
                                            <span
                                                class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">{{ __('Rejected') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $requests->links() }}
                        </div>
                    @else
                        <p class="text-gray-500">{{ __('requests.messages.no_requests') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
