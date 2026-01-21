@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'طلباتي - ' . config('app.name'))

@section('content')
    <div class="khezana-listing-page">
        <div class="khezana-container">
            <!-- Page Header -->
            <div class="khezana-page-header">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--khezana-spacing-md);">
                    <div>
                        <h1 class="khezana-page-title">طلباتي</h1>
                        <p class="khezana-page-subtitle">
                            {{ $requests->total() }} {{ __('requests.plural') ?? 'طلب' }}
                        </p>
                    </div>
                    <a href="{{ route('requests.create') }}" class="khezana-btn khezana-btn-primary">
                        {{ __('requests.create_new') ?? 'إنشاء طلب جديد' }}
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <main class="khezana-listing-main" style="max-width: 100%;">
                @if ($requests->count() > 0)
                    <!-- Requests Grid -->
                    <div class="khezana-requests-grid">
                        @foreach ($requests as $request)
                            <a href="{{ route('requests.show', $request) }}" class="khezana-request-card">
                                <div class="khezana-request-content">
                                    <div class="khezana-request-header">
                                        <h3 class="khezana-request-title">{{ $request->title }}</h3>
                                        <div style="display: flex; gap: var(--khezana-spacing-xs); flex-wrap: wrap;">
                                            <span
                                                class="khezana-request-badge khezana-request-badge-{{ $request->status->value }}">
                                                {{ $request->status->label() }}
                                            </span>

                                            @if($request->approvalRelation)
                                                @php
                                                    $approvalStatus = $request->approvalRelation->status;
                                                    $statusClass = match($approvalStatus->value) {
                                                        'approved' => 'khezana-approval-badge-approved',
                                                        'pending' => 'khezana-approval-badge-pending',
                                                        'rejected' => 'khezana-approval-badge-rejected',
                                                        'archived' => 'khezana-approval-badge-archived',
                                                        default => 'khezana-approval-badge-pending',
                                                    };
                                                @endphp
                                                <span class="khezana-approval-badge {{ $statusClass }}" style="font-size: 0.75rem;">
                                                    {{ $approvalStatus->label() }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($request->category)
                                        <p class="khezana-request-category">{{ $request->category->name }}</p>
                                    @endif

                                    @if ($request->description)
                                        <p class="khezana-request-description">
                                            {{ Str::limit($request->description, 120) }}
                                        </p>
                                    @endif

                                    @if ($request->itemAttributes->count() > 0)
                                        <div class="khezana-request-attributes">
                                            @foreach ($request->itemAttributes->take(3) as $itemAttr)
                                                <span class="khezana-request-attribute">
                                                    <strong>{{ $itemAttr->attribute->name }}:</strong> {{ $itemAttr->value }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="khezana-request-footer">
                                        <div class="khezana-request-meta">
                                            <span class="khezana-request-date">
                                                {{ $request->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        @if ($request->offers->count() > 0)
                                            <span class="khezana-request-offers">
                                                {{ $request->offers->count() }} {{ __('requests.offers') ?? 'عرض' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if ($requests->hasPages())
                        <div class="khezana-pagination">
                            {{ $requests->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="khezana-empty-state">
                        <div class="khezana-empty-icon">📝</div>
                        <h3 class="khezana-empty-title">لا توجد طلبات</h3>
                        <p class="khezana-empty-text">
                            لم تقم بإنشاء أي طلبات بعد. ابدأ بإنشاء أول طلب لك!
                        </p>
                        <a href="{{ route('requests.create') }}" class="khezana-btn khezana-btn-primary">
                            {{ __('requests.create_new') ?? 'إنشاء طلب جديد' }}
                        </a>
                    </div>
                @endif
            </main>
        </div>
    </div>
@endsection
