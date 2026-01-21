<!-- Footer -->
<footer class="khezana-footer">
    <div class="khezana-container">
        <div class="khezana-footer-content">
            <div class="khezana-footer-section">
                <h3 class="khezana-footer-title">{{ config('app.name') }}</h3>
                <p class="khezana-footer-text">
                    {{ __('common.footer.description') }}
                </p>
            </div>

            <div class="khezana-footer-section">
                <h4 class="khezana-footer-heading">{{ __('common.footer.quick_links') }}</h4>
                <ul class="khezana-footer-links">
                    <li><a href="{{ route('public.items.index', ['operation_type' => 'sell']) }}">{{ __('items.operation_types.sell') }}</a></li>
                    <li><a href="{{ route('public.items.index', ['operation_type' => 'rent']) }}">{{ __('items.operation_types.rent') }}</a></li>
                    <li><a href="{{ route('public.items.index', ['operation_type' => 'donate']) }}">{{ __('items.operation_types.donate') }}</a></li>
                    <li><a href="{{ route('public.requests.index') }}">{{ __('requests.title') }}</a></li>
                </ul>
            </div>

            <div class="khezana-footer-section">
                <h4 class="khezana-footer-heading">{{ __('common.footer.information') }}</h4>
                <ul class="khezana-footer-links">
                    <li><a href="#">{{ __('common.ui.about_us') }}</a></li>
                    <li><a href="#">{{ __('common.ui.contact_us') }}</a></li>
                    <li><a href="#">{{ __('common.ui.help') }}</a></li>
                </ul>
            </div>
        </div>

        <div class="khezana-footer-trust">
            <div class="khezana-footer-trust-item">
                <span class="khezana-footer-trust-icon">✓</span>
                <span class="khezana-footer-trust-text">{{ __('common.trust.reviewed') }}</span>
            </div>
            <div class="khezana-footer-trust-item">
                <span class="khezana-footer-trust-icon">🔒</span>
                <span class="khezana-footer-trust-text">{{ __('common.trust.secure') }}</span>
            </div>
        </div>

        <div class="khezana-footer-bottom">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('common.ui.all_rights_reserved') }}.</p>
        </div>
    </div>
</footer>
