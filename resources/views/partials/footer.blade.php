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
                    <li><a href="{{ route('pages.how-it-works') }}">{{ __('pages.how_it_works.title') }}</a></li>
                    <li><a href="{{ route('pages.fees') }}">{{ __('pages.fees.title') }}</a></li>
                    <li><a href="{{ route('pages.terms') }}">{{ __('pages.terms.title') }}</a></li>
                    <li><a href="{{ route('pages.privacy') }}">{{ __('pages.privacy.title') }}</a></li>
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
            <div class="khezana-footer-credits">
                <p class="khezana-footer-credit-text">
                    {{ __('common.footer.developed_by') }} <strong>TichnoVsky</strong>
                </p>
                <p class="khezana-footer-contact">
                    <a href="tel:+963959378002" class="khezana-footer-contact-link">
                        <span class="khezana-footer-contact-icon">📞</span>
                        +963959378002
                    </a>
                </p>
            </div>
        </div>
    </div>
</footer>
