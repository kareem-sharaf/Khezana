@extends('layouts.app')

@section('title', $item->title . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-item-detail-page">
        <div class="khezana-container">
            <!-- Breadcrumb -->
            <nav class="khezana-breadcrumb">
                <a href="{{ route('home') }}">{{ __('common.ui.home') }}</a>
                <span>/</span>
                <a href="{{ route('public.items.index') }}">{{ __('common.ui.items_page') }}</a>
                <span>/</span>
                <span>{{ $item->title }}</span>
            </nav>

            <div class="khezana-item-detail-layout">
                <!-- Images Section -->
                <div class="khezana-item-images">
                    @if ($item->images->count() > 0)
                        <!-- Main Image -->
                        <div class="khezana-item-main-image">
                            <img id="mainImage" src="{{ asset('storage/' . $item->primaryImage->path) }}"
                                alt="{{ $item->title }}" class="khezana-main-img" loading="eager">
                        </div>

                        <!-- Thumbnails (if more than one image) -->
                        @if ($item->images->count() > 1)
                            <div class="khezana-item-thumbnails">
                                @foreach ($item->images as $image)
                                    <button type="button" class="khezana-thumbnail {{ $loop->first ? 'active' : '' }}"
                                        onclick="changeMainImage('{{ asset('storage/' . $image->path) }}', this)">
                                        <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $item->title }}"
                                            loading="lazy">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="khezana-item-main-image khezana-no-image">
                            <div class="khezana-no-image-placeholder">
                                {{ __('common.ui.no_image') }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Details Section -->
                <div class="khezana-item-details">
                    <!-- Header -->
                    <div class="khezana-item-header">
                        <h1 class="khezana-item-detail-title">{{ $item->title }}</h1>
                        <span class="khezana-item-badge khezana-item-badge-{{ $item->operationType }}">
                            {{ __('items.operation_types.' . $item->operationType) }}
                        </span>
                    </div>

                    <!-- Price -->
                    <div class="khezana-item-price-section">
                        @if ($item->operationType == 'donate')
                            <div class="khezana-item-price khezana-item-price-free">
                                <span class="khezana-price-label">{{ __('common.ui.free') }}</span>
                            </div>
                        @elseif($item->price)
                            <div class="khezana-item-price">
                                <span class="khezana-price-amount">{{ number_format($item->price, 0) }}</span>
                                <span class="khezana-price-currency">{{ __('common.ui.currency') }}</span>
                                @if ($item->operationType == 'rent')
                                    <span class="khezana-price-unit">{{ __('common.ui.per_day') }}</span>
                                @endif
                            </div>
                            @if ($item->operationType == 'rent' && $item->depositAmount)
                                <div class="khezana-item-deposit">
                                    <span class="khezana-deposit-label">{{ __('common.ui.deposit') }}:</span>
                                    <span class="khezana-deposit-amount">{{ number_format($item->depositAmount, 0) }}
                                        {{ __('common.ui.currency') }}</span>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Category -->
                    @if ($item->category)
                        <div class="khezana-item-meta">
                            <span class="khezana-meta-label">{{ __('common.ui.category') }}:</span>
                            <span class="khezana-meta-value">{{ $item->category->name }}</span>
                        </div>
                    @endif

                    <!-- Attributes -->
                    @if ($item->attributes->count() > 0)
                        <div class="khezana-item-attributes">
                            <h3 class="khezana-section-title-small">{{ __('common.ui.specifications') }}</h3>
                            <div class="khezana-attributes-grid">
                                @foreach ($item->attributes as $attribute)
                                    <div class="khezana-attribute-item">
                                        <span class="khezana-attribute-name">{{ $attribute->name }}:</span>
                                        <span class="khezana-attribute-value">{{ $attribute->formattedValue }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Description -->
                    @if ($item->description)
                        <div class="khezana-item-description">
                            <h3 class="khezana-section-title-small">{{ __('common.ui.description') }}</h3>
                            <p class="khezana-description-text">{{ $item->description }}</p>
                        </div>
                    @endif

                    <!-- CTA Button -->
                    <div class="khezana-item-cta">
                        @auth
                            <button type="button" onclick="showContactForm()"
                                class="khezana-btn khezana-btn-primary khezana-btn-large khezana-btn-full">
                                {{ __('common.ui.contact_seller') }}
                            </button>
                        @else
                            <a href="{{ route('login') }}"
                                class="khezana-btn khezana-btn-primary khezana-btn-large khezana-btn-full">
                                {{ __('common.ui.register_to_continue') }}
                            </a>
                        @endauth
                    </div>

                    <!-- Contact Form (Hidden by default) -->
                    @auth
                        <div id="contactForm" class="khezana-contact-form" style="display: none;">
                            <h3 class="khezana-section-title-small">{{ __('common.ui.contact_seller') }}</h3>
                            <form method="POST" action="{{ route('public.items.contact', $item->id) }}">
                                @csrf
                                <div class="khezana-form-group">
                                    <label class="khezana-form-label">{{ __('common.ui.your_message') }}</label>
                                    <textarea name="message" class="khezana-form-input" rows="4" required placeholder="{{ __('common.ui.write_your_message') }}"></textarea>
                                </div>
                                <div class="khezana-form-actions">
                                    <button type="submit" class="khezana-btn khezana-btn-primary">
                                        {{ __('common.actions.save') }}
                                    </button>
                                    <button type="button" onclick="hideContactForm()"
                                        class="khezana-btn khezana-btn-secondary">
                                        {{ __('common.actions.cancel') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endauth

                    <!-- Additional Info -->
                    <div class="khezana-item-additional-info">
                        <div class="khezana-info-item">
                            <span class="khezana-info-icon">📅</span>
                            <span class="khezana-info-text">{{ __('common.ui.published') }} {{ $item->createdAtFormatted }}</span>
                        </div>
                        @if ($item->user)
                            <div class="khezana-info-item">
                                <span class="khezana-info-icon">👤</span>
                                <span class="khezana-info-text">{{ __('common.ui.from') }} {{ $item->user->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple JavaScript for Image Gallery and Contact Form -->
    <script>
        function changeMainImage(src, element) {
            document.getElementById('mainImage').src = src;

            // Update active thumbnail
            document.querySelectorAll('.khezana-thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            element.classList.add('active');
        }

        function showContactForm() {
            document.getElementById('contactForm').style.display = 'block';
            document.getElementById('contactForm').scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }

        function hideContactForm() {
            document.getElementById('contactForm').style.display = 'none';
        }
    </script>
@endsection
