{{-- Public Item Detail Scripts Partial --}}
{{-- Usage: @include('public.items._partials.detail.scripts', ['viewModel' => $viewModel]) --}}

<script>
    (function() {
        'use strict';

        const gallery = {
            images: @json(array_column($viewModel->imageUrls, 'url')),
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

                if (this.mainImage) {
                    this.mainImage.style.opacity = '0';
                    setTimeout(() => {
                        this.mainImage.src = src;
                        this.mainImage.setAttribute('data-image-index', index);
                        this.mainImage.setAttribute('data-full-image', src);
                        this.mainImage.style.opacity = '1';
                    }, 150);
                }

                this.thumbnails.forEach((thumb, i) => {
                    thumb.classList.toggle('active', i === index);
                });

                const currentIndexEl = document.getElementById('currentImageIndex');
                if (currentIndexEl) currentIndexEl.textContent = index + 1;

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

        window.changeMainImage = function(src, index, element) {
            gallery.changeMainImage(src, index, element);
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => gallery.init());
        } else {
            gallery.init();
        }
    })();

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
