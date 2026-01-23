{{-- Requests Grid Partial --}}
{{-- Usage: @include('requests._partials.grid', ['requests' => $requests]) --}}

@php
    use Illuminate\Support\Str;
@endphp

<div class="khezana-requests-grid" role="list">
    @foreach ($requests as $request)
        <a href="{{ route('requests.show', $request) }}" class="khezana-request-card" role="listitem">
            <div class="khezana-request-content">
                <div class="khezana-request-header">
                    <h3 class="khezana-request-title">{{ $request->title }}</h3>
                    <div style="display: flex; gap: var(--khezana-spacing-xs); flex-wrap: wrap;">
                        <span class="khezana-request-badge khezana-request-badge-{{ $request->status->value }}">
                            {{ $request->status->label() }}
                        </span>

                        @if ($request->approvalRelation)
                            @php
                                $approvalStatus = $request->approvalRelation->status;
                                $statusClass = match ($approvalStatus->value) {
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
                                <strong>{{ $itemAttr->attribute->name }}:</strong>
                                {{ $itemAttr->value }}
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
                            {{ $request->offers->count() }} {{ __('common.ui.offers') }}
                        </span>
                    @endif
                </div>
            </div>
        </a>
    @endforeach
</div>
