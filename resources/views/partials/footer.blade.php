<!-- Footer -->
<footer class="khezana-footer">
    <div class="khezana-container">
        <div class="khezana-footer-content">
            <div class="khezana-footer-section">
                <h3 class="khezana-footer-title">خزانة</h3>
                <p class="khezana-footer-text">
                    منصة موثوقة للملابس في سوريا - بيع، تأجير، تبرع، وطلب ملابس
                </p>
            </div>
            
            <div class="khezana-footer-section">
                <h4 class="khezana-footer-heading">روابط سريعة</h4>
                <ul class="khezana-footer-links">
                    <li><a href="{{ route('public.items.index', ['operation_type' => 'sell']) }}">بيع</a></li>
                    <li><a href="{{ route('public.items.index', ['operation_type' => 'rent']) }}">إيجار</a></li>
                    <li><a href="{{ route('public.items.index', ['operation_type' => 'donate']) }}">تبرع</a></li>
                    <li><a href="{{ route('public.requests.index') }}">طلبات</a></li>
                </ul>
            </div>
            
            <div class="khezana-footer-section">
                <h4 class="khezana-footer-heading">معلومات</h4>
                <ul class="khezana-footer-links">
                    <li><a href="#">{{ __('common.ui.about_us') ?? 'من نحن' }}</a></li>
                    <li><a href="#">{{ __('common.ui.contact_us') ?? 'اتصل بنا' }}</a></li>
                    <li><a href="#">{{ __('common.ui.help') ?? 'مساعدة' }}</a></li>
                </ul>
            </div>
        </div>
        
        <div class="khezana-footer-bottom">
            <p>&copy; {{ date('Y') }} خزانة. {{ __('common.ui.all_rights_reserved') ?? 'جميع الحقوق محفوظة' }}</p>
        </div>
    </div>
</footer>
