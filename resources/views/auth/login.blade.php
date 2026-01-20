@extends('layouts.auth')

@section('title', 'تسجيل الدخول')

@section('content')
    <div class="khezana-login-page">
        <div class="khezana-login-container">
            <!-- Welcome Message -->
            <div class="khezana-login-header">
                <h1 class="khezana-login-title">مرحباً بك</h1>
                @if (isset($message) && $message)
                    <div class="khezana-auth-message">
                        <span class="khezana-auth-message-icon">🔐</span>
                        <p class="khezana-auth-message-text">{{ $message }}</p>
                    </div>
                @else
                    <p class="khezana-login-subtitle">سجّل دخولك للوصول إلى حسابك</p>
                @endif
            </div>

            <!-- Login Form -->
            <div class="khezana-login-card">
                <form method="POST" action="{{ route('login') }}" class="khezana-login-form" id="loginForm">
                    @csrf

                    <!-- Phone Number Input -->
                    <div class="khezana-form-group">
                        <label for="phone" class="khezana-form-label">رقم الهاتف</label>
                        <input type="tel" id="phone" name="phone"
                            class="khezana-form-input @error('phone') khezana-input-error @enderror"
                            placeholder="09xxxxxxxx" value="{{ old('phone') }}" required autofocus autocomplete="tel">
                        @error('phone')
                            <span class="khezana-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- OTP Code Input (Initially Hidden) -->
                    <div class="khezana-form-group" id="otpGroup" style="display: none;">
                        <label for="otp_code" class="khezana-form-label">رمز التحقق</label>
                        <div class="khezana-otp-container">
                            <input type="text" id="otp_code" name="otp_code"
                                class="khezana-form-input khezana-otp-input @error('otp_code') khezana-input-error @enderror"
                                placeholder="000000" maxlength="6" pattern="[0-9]{6}" autocomplete="one-time-code">
                        </div>
                        @error('otp_code')
                            <span class="khezana-form-error">{{ $message }}</span>
                        @enderror
                        <p class="khezana-otp-hint">
                            لم تستلم الرمز؟
                            <button type="button" id="resendOtp" class="khezana-link-button">إعادة الإرسال</button>
                        </p>
                    </div>

                    <!-- Password Input (Fallback - Shown if OTP not available) -->
                    <div class="khezana-form-group" id="passwordGroup">
                        <label for="password" class="khezana-form-label">كلمة المرور</label>
                        <input type="password" id="password" name="password"
                            class="khezana-form-input @error('password') khezana-input-error @enderror"
                            placeholder="••••••••" autocomplete="current-password" required>
                        @error('password')
                            <span class="khezana-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="khezana-form-actions">
                        <button type="submit" class="khezana-btn khezana-btn-primary khezana-btn-block" id="submitBtn">
                            <span id="submitText">تسجيل الدخول</span>
                            <span id="submitLoading" style="display: none;">جاري التحقق...</span>
                        </button>
                    </div>

                    <!-- Alternative: Use OTP -->
                    <div class="khezana-form-actions" style="margin-top: var(--khezana-spacing-sm);">
                        <button type="button" class="khezana-link-button" id="useOtpBtn"
                            style="width: 100%; text-align: center;">
                            أو استخدم رمز التحقق
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

                <!-- Register Link -->
                <div class="khezana-auth-footer">
                    <p class="khezana-auth-footer-text">
                        ليس لديك حساب؟
                        <a href="{{ route('register', isset($redirect) && $redirect ? ['redirect' => $redirect] : []) }}"
                            class="khezana-link">إنشاء حساب جديد</a>
                    </p>
                    @if (isset($redirect) && $redirect)
                        <p class="khezana-auth-footer-hint">
                            بعد تسجيل الدخول أو إنشاء حساب، سيتم توجيهك تلقائياً لإتمام العملية
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('loginForm');
                const phoneInput = document.getElementById('phone');
                const otpGroup = document.getElementById('otpGroup');
                const passwordGroup = document.getElementById('passwordGroup');
                const otpInput = document.getElementById('otp_code');
                const passwordInput = document.getElementById('password');
                const submitBtn = document.getElementById('submitBtn');
                const submitText = document.getElementById('submitText');
                const submitLoading = document.getElementById('submitLoading');
                const resendBtn = document.getElementById('resendOtp');
                const useOtpBtn = document.getElementById('useOtpBtn');

                let isOtpMode = false;

                // Switch to OTP mode
                useOtpBtn.addEventListener('click', function() {
                    isOtpMode = true;
                    passwordGroup.style.display = 'none';
                    passwordInput.required = false;
                    otpGroup.style.display = 'block';
                    otpInput.required = true;
                    submitText.textContent = 'إرسال رمز التحقق';
                    useOtpBtn.style.display = 'none';
                    phoneInput.focus();
                });

                // Handle form submission
                form.addEventListener('submit', function(e) {
                    if (isOtpMode && !otpInput.value) {
                        e.preventDefault();
                        sendOtp();
                    } else {
                        // Show loading for password login
                        submitBtn.disabled = true;
                        submitText.style.display = 'none';
                        submitLoading.style.display = 'inline';
                    }
                });

                // Send OTP
                function sendOtp() {
                    const phone = phoneInput.value.trim();

                    if (!phone) {
                        return;
                    }

                    // Show loading
                    submitBtn.disabled = true;
                    submitText.style.display = 'none';
                    submitLoading.style.display = 'inline';

                    // TODO: Replace with actual OTP API call
                    // For now, simulate OTP sending
                    setTimeout(() => {
                        submitText.textContent = 'تسجيل الدخول';
                        submitBtn.disabled = false;
                        submitText.style.display = 'inline';
                        submitLoading.style.display = 'none';
                        otpInput.focus();
                    }, 1000);
                }

                // Resend OTP
                if (resendBtn) {
                    resendBtn.addEventListener('click', function() {
                        sendOtp();
                    });
                }

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

                // Auto-submit OTP when 6 digits entered
                otpInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    e.target.value = value;

                    if (value.length === 6 && isOtpMode) {
                        // Auto-submit after a short delay
                        setTimeout(() => {
                            form.submit();
                        }, 300);
                    }
                });
            });
        </script>
    @endpush
@endsection
