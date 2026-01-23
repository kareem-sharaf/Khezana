/**
 * Khezana Filters - Mobile Toggle & Interactions
 * Progressive enhancement for filters component
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFilters);
    } else {
        initFilters();
    }

    function initFilters() {
        const filtersToggle = document.querySelector('[data-filters-toggle]');
        const filters = document.querySelector('[data-filters]');
        const filtersClose = document.querySelector('[data-filters-close]');
        const filtersOverlay = document.querySelector('[data-filters-overlay]');
        const filtersForm = document.querySelector('[data-filters-form]');

        if (!filtersToggle || !filters || !filtersOverlay) {
            return; // Filters not present on this page
        }

        // Toggle filters visibility
        function openFilters() {
            filters.classList.add('is-active');
            filtersOverlay.classList.add('is-active');
            document.body.style.overflow = 'hidden';
            filtersToggle.setAttribute('aria-expanded', 'true');
        }

        function closeFilters() {
            filters.classList.remove('is-active');
            filtersOverlay.classList.remove('is-active');
            document.body.style.overflow = '';
            filtersToggle.setAttribute('aria-expanded', 'false');
        }

        // Event listeners
        filtersToggle.addEventListener('click', function(e) {
            e.preventDefault();
            if (filters.classList.contains('is-active')) {
                closeFilters();
            } else {
                openFilters();
            }
        });

        if (filtersClose) {
            filtersClose.addEventListener('click', function(e) {
                e.preventDefault();
                closeFilters();
            });
        }

        // Close on overlay click
        filtersOverlay.addEventListener('click', function(e) {
            e.preventDefault();
            closeFilters();
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && filters.classList.contains('is-active')) {
                closeFilters();
            }
        });

        // Prevent form submission from closing on mobile (let it submit normally)
        if (filtersForm) {
            filtersForm.addEventListener('submit', function() {
                // On mobile, keep filters open during submission for better UX
                // The page will reload with new filters applied
                if (window.innerWidth <= 1023) {
                    // Small delay to show loading state
                    setTimeout(function() {
                        closeFilters();
                    }, 100);
                }
            });
        }

        // Update active filters count badge
        function updateFiltersBadge() {
            const form = filtersForm;
            if (!form) return;

            const formData = new FormData(form);
            let activeCount = 0;

            // Count non-empty filter values
            for (const [key, value] of formData.entries()) {
                // Skip hidden fields and empty values
                if (key !== 'sort' && key !== 'per_page' && value && value.trim() !== '') {
                    activeCount++;
                }
            }

            const badge = filtersToggle.querySelector('.khezana-filters-toggle__badge');
            if (activeCount > 0) {
                if (!badge) {
                    const badgeEl = document.createElement('span');
                    badgeEl.className = 'khezana-filters-toggle__badge';
                    badgeEl.textContent = activeCount;
                    filtersToggle.appendChild(badgeEl);
                } else {
                    badge.textContent = activeCount;
                }
            } else if (badge) {
                badge.remove();
            }
        }

        // Update badge on input change
        if (filtersForm) {
            const inputs = filtersForm.querySelectorAll('select, input[type="number"]');
            inputs.forEach(function(input) {
                input.addEventListener('change', updateFiltersBadge);
            });

            // Initial badge update
            updateFiltersBadge();
        }

        // Price Range Slider
        initPriceSlider();

        // Active Filters Chips
        initFilterChips();
    }

    function initPriceSlider() {
        const sliderMin = document.querySelector('[data-slider-min]');
        const sliderMax = document.querySelector('[data-slider-max]');
        const inputMin = document.querySelector('[data-price-min]');
        const inputMax = document.querySelector('[data-price-max]');
        const range = document.querySelector('[data-price-range]');
        const displayMin = document.querySelector('[data-price-display-min]');
        const displayMax = document.querySelector('[data-price-display-max]');

        if (!sliderMin || !sliderMax || !inputMin || !inputMax || !range) {
            return;
        }

        const minValue = parseInt(sliderMin.value);
        const maxValue = parseInt(sliderMax.value);
        const minGap = 1000;

        function updateRange() {
            const minVal = parseInt(sliderMin.value);
            const maxVal = parseInt(sliderMax.value);

            if (maxVal - minVal < minGap) {
                if (sliderMin === document.activeElement) {
                    sliderMin.value = maxVal - minGap;
                } else {
                    sliderMax.value = minVal + minGap;
                }
            }

            const minPercent = ((parseInt(sliderMin.value) - parseInt(sliderMin.min)) / (parseInt(sliderMin.max) - parseInt(sliderMin.min))) * 100;
            const maxPercent = ((parseInt(sliderMax.value) - parseInt(sliderMax.min)) / (parseInt(sliderMax.max) - parseInt(sliderMax.min))) * 100;

            range.style.left = minPercent + '%';
            range.style.width = (maxPercent - minPercent) + '%';

            // Update inputs
            inputMin.value = sliderMin.value;
            inputMax.value = sliderMax.value;

            // Update display
            if (displayMin) {
                displayMin.textContent = parseInt(sliderMin.value).toLocaleString();
            }
            if (displayMax) {
                displayMax.textContent = parseInt(sliderMax.value).toLocaleString();
            }
        }

        sliderMin.addEventListener('input', updateRange);
        sliderMax.addEventListener('input', updateRange);

        // Sync inputs with sliders
        inputMin.addEventListener('input', function() {
            const value = parseInt(inputMin.value);
            if (value >= parseInt(sliderMin.min) && value <= parseInt(sliderMax.value) - minGap) {
                sliderMin.value = value;
                updateRange();
            }
        });

        inputMax.addEventListener('input', function() {
            const value = parseInt(inputMax.value);
            if (value <= parseInt(sliderMax.max) && value >= parseInt(sliderMin.value) + minGap) {
                sliderMax.value = value;
                updateRange();
            }
        });

        // Initial update
        updateRange();
    }

    function initFilterChips() {
        const removeButtons = document.querySelectorAll('[data-filter-remove]');

        removeButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const filterKey = button.getAttribute('data-filter-remove');
                const filterValue = button.getAttribute('data-filter-value');
                const form = document.querySelector('[data-filters-form]');

                if (!form) return;

                // Remove the filter from form
                if (filterKey === 'price') {
                    const priceMin = form.querySelector('[name="price_min"]');
                    const priceMax = form.querySelector('[name="price_max"]');
                    if (priceMin) priceMin.value = '';
                    if (priceMax) priceMax.value = '';
                } else if (filterKey === 'search') {
                    const searchInput = form.querySelector('[name="search"]');
                    if (searchInput) searchInput.value = '';
                } else {
                    const filterInput = form.querySelector('[name="' + filterKey + '"]');
                    if (filterInput) {
                        if (filterInput.tagName === 'SELECT') {
                            filterInput.selectedIndex = 0;
                        } else {
                            filterInput.value = '';
                        }
                    }
                }

                // Submit form to apply changes
                form.submit();
            });
        });
    }
})();
