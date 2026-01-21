<!-- Header -->
<header class="khezana-header">
    <nav class="khezana-nav">
        <div class="khezana-container">
            <div class="khezana-nav-content">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="khezana-logo">
                    <span class="khezana-logo-text">خزانة</span>
                </a>
                
                <!-- Navigation Links -->
                <div class="khezana-nav-links">
                    <a href="{{ route('public.items.index', ['operation_type' => 'sell']) }}" class="khezana-nav-link">
                        {{ __('items.operation_types.sell') ?? 'بيع' }}
                    </a>
                    <a href="{{ route('public.items.index', ['operation_type' => 'rent']) }}" class="khezana-nav-link">
                        {{ __('items.operation_types.rent') ?? 'إيجار' }}
                    </a>
                    <a href="{{ route('public.items.index', ['operation_type' => 'donate']) }}" class="khezana-nav-link">
                        {{ __('items.operation_types.donate') ?? 'تبرع' }}
                    </a>
                    <a href="{{ route('public.requests.index') }}" class="khezana-nav-link">
                        {{ __('requests.title') ?? 'طلبات' }}
                    </a>
                </div>
                
                <!-- Actions -->
                <div class="khezana-nav-actions">
                    @auth
                        <div class="khezana-user-box">
                            <div class="khezana-user-info">
                                <span class="khezana-user-greeting">مرحباً،</span>
                                <span class="khezana-user-name">{{ Auth::user()->name ?? Auth::user()->phone }}</span>
                            </div>
                            <div class="khezana-user-actions">
                                <a href="{{ route('items.create') }}" class="khezana-btn khezana-btn-primary">
                                    {{ __('common.ui.add_item') ?? 'أضف غرض' }}
                                </a>
                                <a href="{{ route('dashboard') }}" class="khezana-btn khezana-btn-secondary">
                                    {{ __('common.ui.dashboard') ?? 'لوحة التحكم' }}
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="khezana-btn khezana-btn-secondary khezana-btn-ghost">
                                        {{ __('common.ui.logout') ?? 'تسجيل الخروج' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="khezana-btn khezana-btn-secondary">
                            {{ __('common.ui.login') ?? 'تسجيل الدخول' }}
                        </a>
                        <a href="{{ route('register') }}" class="khezana-btn khezana-btn-primary">
                            {{ __('common.ui.register') ?? 'إنشاء حساب' }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>
