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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
