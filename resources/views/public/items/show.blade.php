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
                <div class="khezana-item-images" id="itemGallery">
                    @if ($item->images->count() > 0)
                        <!-- Main Image -->
                        <div class="khezana-item-main-image" id="mainImageContainer">
                            <button type="button" class="khezana-image-zoom-trigger" id="zoomTrigger" aria-label="{{ __('common.ui.zoom_image') }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="m21 21-4.35-4.35"/>
                                    <line x1="11" y1="8" x2="11" y2="14"/>
                                    <line x1="8" y1="11" x2="14" y2="11"/>
                                </svg>
                            </button>
                            <img id="mainImage"
                                src="{{ asset('storage/' . $item->primaryImage->path) }}"
                                alt="{{ $item->title }}"
                                class="khezana-main-img"
                                loading="eager"
                                data-image-index="0"
                                data-full-image="{{ asset('storage/' . $item->primaryImage->path) }}">
                            @if ($item->images->count() > 1)
                                <div class="khezana-image-navigation">
                                    <button type="button" class="khezana-image-nav-btn khezana-image-nav-prev" id="prevImage" aria-label="{{ __('common.ui.previous_image') }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <polyline points="15 18 9 12 15 6"/>
                                        </svg>
                                    </button>
                                    <button type="button" class="khezana-image-nav-btn khezana-image-nav-next" id="nextImage" aria-label="{{ __('common.ui.next_image') }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <polyline points="9 18 15 12 9 6"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="khezana-image-counter">
                                    <span id="currentImageIndex">1</span> / <span id="totalImages">{{ $item->images->count() }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Thumbnails (if more than one image) -->
                        @if ($item->images->count() > 1)
                            <div class="khezana-item-thumbnails" id="thumbnailsContainer">
                                @foreach ($item->images as $image)
                                    <button type="button"
                                        class="khezana-thumbnail {{ $loop->first ? 'active' : '' }}"
                                        data-image-index="{{ $loop->index }}"
                                        data-image-src="{{ asset('storage/' . $image->path) }}"
                                        aria-label="{{ __('common.ui.view_image') }} {{ $loop->iteration }}"
                                        onclick="changeMainImage('{{ asset('storage/' . $image->path) }}', {{ $loop->index }}, this)">
                                        <img src="{{ asset('storage/' . $image->path) }}"
                                            alt="{{ $item->title }} - {{ __('common.ui.image') }} {{ $loop->iteration }}"
                                            loading="lazy">
                                        <span class="khezana-thumbnail-overlay"></span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="khezana-item-main-image khezana-no-image">
                            <div class="khezana-no-image-placeholder">
                                <svg class="khezana-placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                                <span class="khezana-placeholder-text">{{ __('common.ui.no_image') }}</span>
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
                            @php $displayPrice = price_with_fee((float) $item->price, $item->operationType); @endphp
                            @if ($displayPrice !== null)
                            <div class="khezana-item-price">
                                <span class="khezana-price-amount">{{ number_format($displayPrice, 0) }}</span>
                                <span class="khezana-price-currency">{{ __('common.ui.currency') }}</span>
                                @if ($item->operationType == 'rent')
                                    <span class="khezana-price-unit">{{ __('common.ui.per_day') }}</span>
                                @endif
                            </div>
                            @endif
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

                    <!-- Condition -->
                    @if ($item->condition)
                        <div class="khezana-item-meta">
                            <span class="khezana-meta-label">🏷️ {{ __('items.fields.condition') }}:</span>
                            <span class="khezana-meta-value">{{ __('items.conditions.' . $item->condition) }}</span>
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

    <!-- Fullscreen Image Modal -->
    <div class="khezana-image-modal" id="imageModal" role="dialog" aria-modal="true" aria-label="{{ __('common.ui.image_gallery') }}" style="display: none;">
        <div class="khezana-image-modal-overlay" id="modalOverlay"></div>
        <div class="khezana-image-modal-content">
            <button type="button" class="khezana-image-modal-close" id="closeModal" aria-label="{{ __('common.ui.close') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <button type="button" class="khezana-image-modal-nav khezana-image-modal-prev" id="modalPrev" aria-label="{{ __('common.ui.previous_image') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </button>
            <div class="khezana-image-modal-image-container">
                <img id="modalImage" src="" alt="" class="khezana-image-modal-img">
                <div class="khezana-image-modal-loader" id="modalLoader">
                    <div class="khezana-modal-skeleton"></div>
                </div>
            </div>
            <button type="button" class="khezana-image-modal-nav khezana-image-modal-next" id="modalNext" aria-label="{{ __('common.ui.next_image') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </button>
            <div class="khezana-image-modal-counter">
                <span id="modalImageIndex">1</span> / <span id="modalTotalImages">{{ $item->images->count() ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- Professional Image Gallery JavaScript -->
    <script>
        (function() {
            'use strict';

            const gallery = {
                images: [
                    @foreach($item->images as $image)
                        "{{ asset('storage/' . $image->path) }}"{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],
                currentIndex: 0,
                modal: null,
                mainImage: null,
                thumbnails: null,

                init: function() {
                    if (this.images.length === 0) return;

                    this.mainImage = document.getElementById('mainImage');
                    this.thumbnails = document.querySelectorAll('.khezana-thumbnail');
                    this.modal = document.getElementById('imageModal');

                    this.setupEventListeners();
                    this.setupKeyboardNavigation();
                    this.setupTouchGestures();
                },

                setupEventListeners: function() {
                    // Zoom trigger
                    const zoomTrigger = document.getElementById('zoomTrigger');
                    if (zoomTrigger) {
                        zoomTrigger.addEventListener('click', () => this.openModal(this.currentIndex));
                    }

                    // Main image click
                    if (this.mainImage) {
                        this.mainImage.addEventListener('click', () => this.openModal(this.currentIndex));
                    }

                    // Navigation buttons
                    const prevBtn = document.getElementById('prevImage');
                    const nextBtn = document.getElementById('nextImage');
                    if (prevBtn) prevBtn.addEventListener('click', () => this.navigate(-1));
                    if (nextBtn) nextBtn.addEventListener('click', () => this.navigate(1));

                    // Modal controls
                    const closeBtn = document.getElementById('closeModal');
                    const modalOverlay = document.getElementById('modalOverlay');
                    const modalPrev = document.getElementById('modalPrev');
                    const modalNext = document.getElementById('modalNext');

                    if (closeBtn) closeBtn.addEventListener('click', () => this.closeModal());
                    if (modalOverlay) modalOverlay.addEventListener('click', () => this.closeModal());
                    if (modalPrev) modalPrev.addEventListener('click', () => this.modalNavigate(-1));
                    if (modalNext) modalNext.addEventListener('click', () => this.modalNavigate(1));
                },

                setupKeyboardNavigation: function() {
                    document.addEventListener('keydown', (e) => {
                        if (this.modal && this.modal.style.display !== 'none') {
                            if (e.key === 'Escape') this.closeModal();
                            if (e.key === 'ArrowLeft') this.modalNavigate(-1);
                            if (e.key === 'ArrowRight') this.modalNavigate(1);
                        } else {
                            if (e.key === 'ArrowLeft') this.navigate(-1);
                            if (e.key === 'ArrowRight') this.navigate(1);
                        }
                    });
                },

                setupTouchGestures: function() {
                    if (!this.mainImage) return;

                    let touchStartX = 0;
                    let touchEndX = 0;

                    this.mainImage.addEventListener('touchstart', (e) => {
                        touchStartX = e.changedTouches[0].screenX;
                    }, { passive: true });

                    this.mainImage.addEventListener('touchend', (e) => {
                        touchEndX = e.changedTouches[0].screenX;
                        const diff = touchStartX - touchEndX;

                        if (Math.abs(diff) > 50) {
                            if (diff > 0) this.navigate(1);
                            else this.navigate(-1);
                        }
                    }, { passive: true });
                },

                changeMainImage: function(src, index, element) {
                    this.currentIndex = index;

                    // Fade transition
                    if (this.mainImage) {
                        this.mainImage.style.opacity = '0';
                        setTimeout(() => {
                            this.mainImage.src = src;
                            this.mainImage.setAttribute('data-image-index', index);
                            this.mainImage.setAttribute('data-full-image', src);
                            this.mainImage.style.opacity = '1';
                        }, 150);
                    }

                    // Update thumbnails
                    this.thumbnails.forEach((thumb, i) => {
                        thumb.classList.toggle('active', i === index);
                    });

                    // Update counter
                    const currentIndexEl = document.getElementById('currentImageIndex');
                    if (currentIndexEl) currentIndexEl.textContent = index + 1;

                    // Scroll thumbnail into view
                    if (element) {
                        element.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    }
                },

                navigate: function(direction) {
                    const newIndex = (this.currentIndex + direction + this.images.length) % this.images.length;
                    const thumbnail = this.thumbnails[newIndex];
                    if (thumbnail) {
                        this.changeMainImage(this.images[newIndex], newIndex, thumbnail);
                    }
                },

                openModal: function(index) {
                    this.currentIndex = index;
                    const modal = this.modal;
                    const modalImage = document.getElementById('modalImage');
                    const loader = document.getElementById('modalLoader');

                    if (!modal || !modalImage) return;

                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';

                    // Show loader
                    if (loader) loader.style.display = 'flex';
                    modalImage.style.opacity = '0';

                    // Load image
                    const img = new Image();
                    img.onload = () => {
                        modalImage.src = this.images[index];
                        modalImage.alt = '{{ $item->title }} - {{ __('common.ui.image') }} ' + (index + 1);
                        modalImage.style.opacity = '1';
                        if (loader) loader.style.display = 'none';
                    };
                    img.src = this.images[index];

                    // Update counter
                    const modalIndex = document.getElementById('modalImageIndex');
                    if (modalIndex) modalIndex.textContent = index + 1;
                },

                closeModal: function() {
                    const modal = this.modal;
                    if (!modal) return;

                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                },

                modalNavigate: function(direction) {
                    const newIndex = (this.currentIndex + direction + this.images.length) % this.images.length;
                    this.openModal(newIndex);
                }
            };

            // Global function for thumbnail clicks
            window.changeMainImage = function(src, index, element) {
                gallery.changeMainImage(src, index, element);
            };

            // Initialize on DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => gallery.init());
            } else {
                gallery.init();
            }
        })();

        // Contact Form Functions
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
