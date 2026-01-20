@extends('layouts.auth')

@section('title', 'إنشاء حساب')

@section('content')
<div class="khezana-login-page">
    <div class="khezana-login-container">
        <!-- Welcome Message -->
        <div class="khezana-login-header">
            <h1 class="khezana-login-title">إنشاء حساب جديد</h1>
            @if(isset($redirect) && $redirect)
                <div class="khezana-auth-message">
                    <span class="khezana-auth-message-icon">✨</span>
                    <p class="khezana-auth-message-text">أنشئ حسابك الآن لإتمام العملية</p>
                </div>
            @else
                <p class="khezana-login-subtitle">انضم إلينا وابدأ استخدام المنصة</p>
            @endif
        </div>

        <!-- Registration Form -->
        <div class="khezana-login-card">
            <form method="POST" action="{{ route('register') }}" class="khezana-login-form">
                @csrf
                
                @if(isset($redirect) && $redirect)
                    <input type="hidden" name="redirect" value="{{ $redirect }}">
                @endif

                <!-- Name Input -->
                <div class="khezana-form-group">
                    <label for="name" class="khezana-form-label">الاسم الكامل</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="khezana-form-input @error('name') khezana-input-error @enderror" 
                        placeholder="أدخل اسمك الكامل"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                    >
                    @error('name')
                        <span class="khezana-form-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Phone Number Input -->
                <div class="khezana-form-group">
                    <label for="phone" class="khezana-form-label">رقم الهاتف</label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        class="khezana-form-input @error('phone') khezana-input-error @enderror" 
                        placeholder="09xxxxxxxx"
                        value="{{ old('phone') }}"
                        required
                        autocomplete="tel"
                    >
                    @error('phone')
                        <span class="khezana-form-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="khezana-form-group">
                    <label for="password" class="khezana-form-label">كلمة المرور</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="khezana-form-input @error('password') khezana-input-error @enderror" 
                        placeholder="••••••••"
                        required
                        autocomplete="new-password"
                    >
                    @error('password')
                        <span class="khezana-form-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Confirmation Input -->
                <div class="khezana-form-group">
                    <label for="password_confirmation" class="khezana-form-label">تأكيد كلمة المرور</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="khezana-form-input" 
                        placeholder="••••••••"
                        required
                        autocomplete="new-password"
                    >
                </div>

                <!-- Submit Button -->
                <div class="khezana-form-actions">
                    <button type="submit" class="khezana-btn khezana-btn-primary khezana-btn-block">
                        إنشاء الحساب
                    </button>
                </div>
            </form>

            <!-- Trust Messages -->
            <div class="khezana-trust-messages">
                <div class="khezana-trust-item">
                    <span class="khezana-trust-icon">🔒</span>
                    <span class="khezana-trust-text">بياناتك محمية ومشفرة</span>
                </div>
                <div class="khezana-trust-item">
                    <span class="khezana-trust-icon">🛡️</span>
                    <span class="khezana-trust-text">نحن لا نشارك معلوماتك مع أحد</span>
                </div>
            </div>

            <!-- Login Link -->
            <div class="khezana-auth-footer">
                <p class="khezana-auth-footer-text">
                    لديك حساب بالفعل؟ 
                    <a href="{{ route('login', isset($redirect) && $redirect ? ['redirect' => $redirect] : []) }}" class="khezana-link">تسجيل الدخول</a>
                </p>
                @if(isset($redirect) && $redirect)
                    <p class="khezana-auth-footer-hint">
                        بعد إنشاء حسابك، سيتم توجيهك تلقائياً لإتمام العملية
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    
    // Format phone number
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.startsWith('0')) {
            value = value.substring(1);
        }
        if (value.length > 0 && !value.startsWith('9')) {
            value = '9' + value;
        }
        e.target.value = value;
    });
});
</script>
@endpush
@endsection
