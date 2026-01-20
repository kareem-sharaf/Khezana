<footer class="site-footer" role="contentinfo">
    <div class="container">
        <div class="footer__content">
            <div class="footer__section">
                <h4 class="footer__title">{{ trans('common.ui.about') }}</h4>
                <ul class="footer__links">
                    <li><a href="/about">{{ trans('common.ui.about_us') }}</a></li>
                    <li><a href="/contact">{{ trans('common.ui.contact_us') }}</a></li>
                    <li><a href="/privacy">{{ trans('common.ui.privacy_policy') }}</a></li>
                    <li><a href="/terms">{{ trans('common.ui.terms_of_service') }}</a></li>
                </ul>
            </div>
            
            <div class="footer__section">
                <h4 class="footer__title">{{ trans('common.navigation.categories') }}</h4>
                <ul class="footer__links">
                    @php
                        $footerCategories = \App\Models\Category::active()->roots()->limit(4)->get();
                    @endphp
                    @foreach($footerCategories as $category)
                        <li>
                            <a href="{{ route('public.items.index', ['category_id' => $category->id]) }}">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <div class="footer__section">
                <h4 class="footer__title">{{ trans('common.ui.help') }}</h4>
                <ul class="footer__links">
                    <li><a href="/help">{{ trans('common.ui.how_to_use') }}</a></li>
                    <li><a href="/faq">{{ trans('common.ui.faq') }}</a></li>
                    <li><a href="/seller-guide">{{ trans('common.ui.seller_guide') }}</a></li>
                    <li><a href="/buyer-guide">{{ trans('common.ui.buyer_guide') }}</a></li>
                </ul>
            </div>
            
            <div class="footer__section">
                <h4 class="footer__title">{{ trans('common.ui.follow_us') }}</h4>
                <div class="footer__social">
                    <a href="#" class="footer__social-link" aria-label="Facebook">📘</a>
                    <a href="#" class="footer__social-link" aria-label="Twitter">🐦</a>
                    <a href="#" class="footer__social-link" aria-label="Instagram">📷</a>
                </div>
            </div>
        </div>
        
        <div class="footer__bottom">
            <p class="footer__copyright">
                © {{ date('Y') }} {{ config('app.name', 'Khezana') }}. {{ trans('common.ui.all_rights_reserved') }}
            </p>
        </div>
    </div>
</footer>

