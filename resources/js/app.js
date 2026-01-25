import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Price Slider Component - Professional Range Selection
// config is provided from PHP (settings). Use config values; fallbacks only when missing.
Alpine.data('priceSlider', (minValue, maxValue, config = {}) => {
    const min = (config.min !== undefined && config.min !== null && !isNaN(Number(config.min))) ? Number(config.min) : 0;
    const max = (config.max !== undefined && config.max !== null && !isNaN(Number(config.max))) ? Number(config.max) : 1000000;
    const minGap = (config.minGap !== undefined && config.minGap !== null && !isNaN(Number(config.minGap))) ? Number(config.minGap) : 1000;
    const step = (config.step !== undefined && config.step !== null && !isNaN(Number(config.step)) && Number(config.step) > 0) ? Number(config.step) : 1000;

    // Use values from parameters or default to min/max from config
    let low = (minValue !== undefined && minValue !== null) ? parseInt(minValue) : min;
    if (isNaN(low)) {
        low = min;
    }
    let high = (maxValue !== undefined && maxValue !== null) ? parseInt(maxValue) : max;

    if (isNaN(high)) {
        high = max;
    }

    // Ensure values are within bounds
    if (low < min) low = min;
    if (high > max) high = max;
    if (low > high) {
        low = min;
        high = max;
    }

    // Ensure minimum gap
    if (high - low < minGap) {
        if (low === min) {
            high = Math.min(max, min + minGap);
        } else {
            low = Math.max(min, high - minGap);
        }
    }

    const initialMin = Math.max(min, Math.min(max, low));
    const initialMax = Math.max(initialMin + minGap, Math.min(max, high));

    return {
        minValue: initialMin,
        maxValue: initialMax,
        min: min,
        max: max,
        minGap: minGap,
        step: step,
        isDragging: false,
        isDraggingMin: false,
        isDraggingMax: false,

        get minPercent() {
            return ((this.minValue - this.min) / (this.max - this.min)) * 100;
        },

        get maxPercent() {
            return ((this.maxValue - this.min) / (this.max - this.min)) * 100;
        },

        get labelsTooClose() {
            return Math.abs(this.maxPercent - this.minPercent) < 12;
        },

        updateMin(value) {
            const newValue = Math.max(this.min, Math.min(this.maxValue - this.minGap, parseInt(value) || this.min));
            this.minValue = newValue;
        },

        updateMax(value) {
            const newValue = Math.max(this.minValue + this.minGap, Math.min(this.max, parseInt(value) || this.max));
            this.maxValue = newValue;
        },

        formatPrice(price) {
            const locale = document.documentElement.lang || 'ar';
            return new Intl.NumberFormat(locale, {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(price);
        },

        onDragStart(which) {
            this.isDragging = true;
            if (which === 'min') this.isDraggingMin = true;
            if (which === 'max') this.isDraggingMax = true;
        },

        onDragEnd(which) {
            if (which === 'min') this.isDraggingMin = false;
            if (which === 'max') this.isDraggingMax = false;
            this.isDragging = false;
            // No auto-submit - user must click "Apply Filters"
        },

        onTrackClick(e) {
            const track = e.currentTarget;
            const rect = track.getBoundingClientRect();
            const percent = Math.max(0, Math.min(100, ((e.clientX - rect.left) / rect.width) * 100));
            const raw = this.min + (this.max - this.min) * (percent / 100);
            const value = Math.round(raw / this.step) * this.step;
            const valueClamped = Math.max(this.min, Math.min(this.max, value));
            const distMin = Math.abs(this.minPercent - percent);
            const distMax = Math.abs(this.maxPercent - percent);
            if (distMin <= distMax) {
                this.updateMin(String(Math.min(valueClamped, this.maxValue - this.minGap)));
            } else {
                this.updateMax(String(Math.max(valueClamped, this.minValue + this.minGap)));
            }
        }
    };
});

// Ensure filters form submission works
document.addEventListener('DOMContentLoaded', function() {
    const filtersForm = document.getElementById('filters-form');
    if (filtersForm) {
        // Make sure form submission works
        filtersForm.addEventListener('submit', function(e) {
            // Allow normal form submission
            console.log('Form submitting...');
        });
    }
});

Alpine.start();
