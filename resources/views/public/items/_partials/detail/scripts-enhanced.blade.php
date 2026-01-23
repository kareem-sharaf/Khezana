{{-- Enhanced Public Item Detail Scripts Partial --}}
{{-- World-class image gallery with hover zoom, magnifier, progressive loading, and smooth transitions --}}
{{-- Usage: @include('public.items._partials.detail.scripts-enhanced', ['viewModel' => $viewModel]) --}}

<script>
    (function() {
        'use strict';

        /**
         * Advanced Image Gallery
         * Features:
         * - Hover Zoom
         * - Magnifier Lens
         * - Smooth Transitions (Fade, Slide, Crossfade)
         * - Progressive Loading
         * - Touch Gestures
         * - Keyboard Navigation
         * - Preloading
         */
        const gallery = {
            images: @json(array_column($viewModel->imageUrls, 'url')),
            currentIndex: 0,
            modal: null,
            mainImage: null,
            heroViewport: null,
            thumbnails: null,
            magnifier: null,
            magnifierImg: null,
            preloadedImages: new Set(),
            transitionType: 'fade', // 'fade', 'slide', 'crossfade'
            zoomLevel: 2, // Default zoom level
            isMagnifierActive: false,
            isHoverZoomEnabled: true,

            init: function() {
                if (this.images.length === 0) return;

                this.heroViewport = document.getElementById('heroViewport');
                this.mainImage = document.getElementById('mainImage');
                this.thumbnails = document.querySelectorAll('.khezana-image-gallery__thumbnail');
                this.modal = document.getElementById('imageModal');
                this.magnifier = document.getElementById('magnifierLens');
                this.magnifierImg = document.getElementById('magnifierImg');

                // Preload first 3 images
                this.preloadImages(0, Math.min(3, this.images.length));

                this.setupEventListeners();
                this.setupKeyboardNavigation();
                this.setupTouchGestures();
                this.setupHoverZoom();
                this.setupMagnifier();
            },

            /**
             * Preload images for better performance
             */
            preloadImages: function(startIndex, count) {
                for (let i = startIndex; i < startIndex + count && i < this.images.length; i++) {
                    if (this.preloadedImages.has(i)) continue;
                    
                    const img = new Image();
                    img.src = this.images[i];
                    this.preloadedImages.add(i);
                }
            },

            /**
             * Setup Event Listeners
             */
            setupEventListeners: function() {
                const zoomTrigger = document.getElementById('zoomTrigger');
                if (zoomTrigger) {
                    zoomTrigger.addEventListener('click', () => this.openModal(this.currentIndex));
                }

                if (this.mainImage) {
                    this.mainImage.addEventListener('click', () => this.openModal(this.currentIndex));
                }

                const prevBtn = document.getElementById('prevImage');
                const nextBtn = document.getElementById('nextImage');
                if (prevBtn) prevBtn.addEventListener('click', () => this.navigate(-1));
                if (nextBtn) nextBtn.addEventListener('click', () => this.navigate(1));

                const closeBtn = document.getElementById('closeModal');
                const modalOverlay = document.getElementById('modalOverlay');
                const modalPrev = document.getElementById('modalPrev');
                const modalNext = document.getElementById('modalNext');

                if (closeBtn) closeBtn.addEventListener('click', () => this.closeModal());
                if (modalOverlay) modalOverlay.addEventListener('click', () => this.closeModal());
                if (modalPrev) modalPrev.addEventListener('click', () => this.modalNavigate(-1));
                if (modalNext) modalNext.addEventListener('click', () => this.modalNavigate(1));
            },

            /**
             * Setup Keyboard Navigation
             */
            setupKeyboardNavigation: function() {
                document.addEventListener('keydown', (e) => {
                    if (this.modal && this.modal.style.display !== 'none') {
                        if (e.key === 'Escape') this.closeModal();
                        if (e.key === 'ArrowLeft') this.modalNavigate(-1);
                        if (e.key === 'ArrowRight') this.modalNavigate(1);
                    } else {
                        if (e.key === 'ArrowLeft') this.navigate(-1);
                        if (e.key === 'ArrowRight') this.navigate(1);
                        if (e.key === 'Home') this.navigateTo(0);
                        if (e.key === 'End') this.navigateTo(this.images.length - 1);
                    }
                });
            },

            /**
             * Setup Touch Gestures
             */
            setupTouchGestures: function() {
                if (!this.mainImage) return;

                let touchStartX = 0;
                let touchStartY = 0;
                let touchEndX = 0;
                let touchEndY = 0;
                let touchStartTime = 0;

                this.mainImage.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                    touchStartY = e.changedTouches[0].screenY;
                    touchStartTime = Date.now();
                }, { passive: true });

                this.mainImage.addEventListener('touchend', (e) => {
                    touchEndX = e.changedTouches[0].screenX;
                    touchEndY = e.changedTouches[0].screenY;
                    const touchDuration = Date.now() - touchStartTime;
                    const diffX = touchStartX - touchEndX;
                    const diffY = touchStartY - touchEndY;

                    // Swipe detection (horizontal swipe > 50px, vertical < 100px)
                    if (Math.abs(diffX) > 50 && Math.abs(diffY) < 100) {
                        if (diffX > 0) this.navigate(1);  // Swipe left = next
                        else this.navigate(-1);           // Swipe right = previous
                    }
                    // Double tap to zoom
                    else if (touchDuration < 300 && Math.abs(diffX) < 10 && Math.abs(diffY) < 10) {
                        this.openModal(this.currentIndex);
                    }
                }, { passive: true });
            },

            /**
             * Setup Hover Zoom
             */
            setupHoverZoom: function() {
                if (!this.heroViewport || !this.isHoverZoomEnabled) return;

                // Disable hover zoom on touch devices
                if ('ontouchstart' in window) {
                    this.heroViewport.classList.remove('khezana-image-gallery__hero--zoom-enabled');
                    return;
                }

                // Hover zoom is handled by CSS
                // Additional JavaScript can be added here for advanced features
            },

            /**
             * Setup Magnifier Lens
             */
            setupMagnifier: function() {
                if (!this.heroViewport || !this.magnifier || !this.magnifierImg) return;

                // Disable magnifier on touch devices
                if ('ontouchstart' in window) return;

                this.heroViewport.addEventListener('mousemove', (e) => {
                    if (!this.isMagnifierActive) return;

                    const rect = this.heroViewport.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;

                    // Calculate magnifier position
                    const magnifierSize = 200;
                    const magnifierHalf = magnifierSize / 2;

                    let magnifierX = x - magnifierHalf;
                    let magnifierY = y - magnifierHalf;

                    // Keep magnifier within bounds
                    magnifierX = Math.max(0, Math.min(magnifierX, rect.width - magnifierSize));
                    magnifierY = Math.max(0, Math.min(magnifierY, rect.height - magnifierSize));

                    this.magnifier.style.left = magnifierX + 'px';
                    this.magnifier.style.top = magnifierY + 'px';

                    // Calculate image position for zoom
                    const imgRect = this.mainImage.getBoundingClientRect();
                    const imgX = ((x / rect.width) * this.mainImage.naturalWidth) - (magnifierHalf * this.zoomLevel);
                    const imgY = ((y / rect.height) * this.mainImage.naturalHeight) - (magnifierHalf * this.zoomLevel);

                    this.magnifierImg.style.transform = `translate(-${imgX}px, -${imgY}px)`;
                });

                // Toggle magnifier on Ctrl/Cmd + Mouse Enter
                this.heroViewport.addEventListener('mouseenter', (e) => {
                    if (e.ctrlKey || e.metaKey) {
                        this.isMagnifierActive = true;
                        this.magnifier.classList.add('khezana-image-gallery__magnifier--active');
                        if (this.magnifierImg) {
                            this.magnifierImg.src = this.images[this.currentIndex];
                        }
                    }
                });

                this.heroViewport.addEventListener('mouseleave', () => {
                    this.isMagnifierActive = false;
                    this.magnifier.classList.remove('khezana-image-gallery__magnifier--active');
                });

                // Toggle with Ctrl/Cmd key
                document.addEventListener('keydown', (e) => {
                    if ((e.ctrlKey || e.metaKey) && this.heroViewport.matches(':hover')) {
                        this.isMagnifierActive = true;
                        this.magnifier.classList.add('khezana-image-gallery__magnifier--active');
                        if (this.magnifierImg) {
                            this.magnifierImg.src = this.images[this.currentIndex];
                        }
                    }
                });

                document.addEventListener('keyup', (e) => {
                    if (!e.ctrlKey && !e.metaKey) {
                        this.isMagnifierActive = false;
                        this.magnifier.classList.remove('khezana-image-gallery__magnifier--active');
                    }
                });
            },

            /**
             * Change Main Image with Smooth Transition
             */
            changeMainImage: function(src, index, element) {
                this.currentIndex = index;

                if (!this.mainImage) return;

                // Preload next images
                this.preloadImages(index + 1, 2);

                // Apply transition based on type
                switch (this.transitionType) {
                    case 'fade':
                        this.fadeTransition(src);
                        break;
                    case 'slide':
                        this.slideTransition(src, element ? (index > this.currentIndex ? 1 : -1) : 0);
                        break;
                    case 'crossfade':
                        this.crossfadeTransition(src);
                        break;
                    default:
                        this.fadeTransition(src);
                }

                // Update thumbnails
                this.thumbnails.forEach((thumb, i) => {
                    thumb.classList.toggle('khezana-image-gallery__thumbnail--active', i === index);
                });

                // Update counter
                const currentIndexEl = document.getElementById('currentImageIndex');
                if (currentIndexEl) currentIndexEl.textContent = index + 1;

                // Scroll thumbnail into view
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }

                // Update magnifier image if active
                if (this.isMagnifierActive && this.magnifierImg) {
                    this.magnifierImg.src = src;
                }
            },

            /**
             * Fade Transition
             */
            fadeTransition: function(src) {
                this.mainImage.classList.add('khezana-image-gallery__hero-img--fade-out');
                
                setTimeout(() => {
                    this.mainImage.src = src;
                    this.mainImage.setAttribute('data-image-index', this.currentIndex);
                    this.mainImage.setAttribute('data-full-image', src);
                    this.mainImage.classList.remove('khezana-image-gallery__hero-img--fade-out');
                    this.mainImage.classList.add('khezana-image-gallery__hero-img--fade-in');
                    
                    // Remove fade-in class after animation
                    setTimeout(() => {
                        this.mainImage.classList.remove('khezana-image-gallery__hero-img--fade-in');
                    }, 400);
                }, 200);
            },

            /**
             * Slide Transition
             */
            slideTransition: function(src, direction) {
                const slideDistance = direction * 100;
                this.mainImage.style.transform = `translateX(${slideDistance}%)`;
                this.mainImage.style.opacity = '0';
                
                setTimeout(() => {
                    this.mainImage.src = src;
                    this.mainImage.setAttribute('data-image-index', this.currentIndex);
                    this.mainImage.setAttribute('data-full-image', src);
                    this.mainImage.style.transform = `translateX(${-slideDistance}%)`;
                    
                    requestAnimationFrame(() => {
                        this.mainImage.style.transition = 'transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                        this.mainImage.style.transform = 'translateX(0)';
                        this.mainImage.style.opacity = '1';
                    });
                }, 50);
            },

            /**
             * Crossfade Transition (for future use)
             */
            crossfadeTransition: function(src) {
                // Similar to fade but with overlay technique
                this.fadeTransition(src);
            },

            /**
             * Navigate to next/previous image
             */
            navigate: function(direction) {
                const newIndex = (this.currentIndex + direction + this.images.length) % this.images.length;
                const thumbnail = this.thumbnails[newIndex];
                if (thumbnail) {
                    this.changeMainImage(this.images[newIndex], newIndex, thumbnail);
                }
            },

            /**
             * Navigate to specific index
             */
            navigateTo: function(index) {
                if (index >= 0 && index < this.images.length) {
                    const thumbnail = this.thumbnails[index];
                    if (thumbnail) {
                        this.changeMainImage(this.images[index], index, thumbnail);
                    }
                }
            },

            /**
             * Open Modal
             */
            openModal: function(index) {
                this.currentIndex = index;
                const modal = this.modal;
                const modalImage = document.getElementById('modalImage');
                const loader = document.getElementById('modalLoader');

                if (!modal || !modalImage) return;

                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';

                if (loader) loader.style.display = 'flex';
                modalImage.style.opacity = '0';

                const img = new Image();
                img.onload = () => {
                    modalImage.src = this.images[index];
                    modalImage.alt = '{{ $viewModel->title }} - {{ __('common.ui.image') }} ' + (index + 1);
                    modalImage.style.opacity = '1';
                    if (loader) loader.style.display = 'none';
                };
                img.src = this.images[index];

                const modalIndex = document.getElementById('modalImageIndex');
                if (modalIndex) modalIndex.textContent = index + 1;
            },

            /**
             * Close Modal
             */
            closeModal: function() {
                const modal = this.modal;
                if (!modal) return;

                modal.style.display = 'none';
                document.body.style.overflow = '';
            },

            /**
             * Navigate in Modal
             */
            modalNavigate: function(direction) {
                const newIndex = (this.currentIndex + direction + this.images.length) % this.images.length;
                this.openModal(newIndex);
            }
        };

        // Global function for backward compatibility
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
