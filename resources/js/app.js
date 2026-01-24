import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Price Slider Component - Professional Range Selection
Alpine.data('priceSlider', (minValue = 0, maxValue = 1000000) => {
    const min = 0;
    const max = 1000000;
    const minGap = 1000; // Minimum gap between min and max

    let low = parseInt(minValue) || min;
    let high = parseInt(maxValue) || max;
    if (low > high) {
        low = min;
        high = max;
    }
    const initialMin = Math.max(min, Math.min(max, low));
    const initialMax = Math.max(initialMin + minGap, Math.min(max, high));

    return {
        minValue: initialMin,
        maxValue: initialMax,
        min: min,
        max: max,
        minGap: minGap,
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
            const step = 1000;
            const raw = this.min + (this.max - this.min) * (percent / 100);
            const value = Math.round(raw / step) * step;
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

Alpine.start();
