<!-- Header -->
<header class="khezana-header">
    <nav class="khezana-nav">
        <div class="khezana-container">
            <div class="khezana-nav-content">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="khezana-logo" aria-label="{{ __('common.ui.main_navigation') }}">
                    @if (file_exists(public_path('logo.svg')))
                        <img src="{{ asset('logo.svg') }}" alt="{{ config('app.name') }}" class="khezana-logo-img"
                            loading="eager" width="180" height="45">
                        <span class="khezana-logo-text">خزانة</span>
                    @elseif(file_exists(public_path('logo.png')))
                        <img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}" class="khezana-logo-img"
                            loading="eager" width="180" height="45">
                        <span class="khezana-logo-text">خزانة</span>
                    @else
                        <span class="khezana-logo-text">{{ config('app.name') }}</span>
                    @endif
                </a>

                <!-- Mobile Menu Toggle -->
                <input type="checkbox" id="mobileMenuToggle" class="khezana-mobile-menu-checkbox" tabindex="-1">
                <label for="mobileMenuToggle" class="khezana-mobile-menu-toggle" aria-label="{{ __('common.ui.menu') }}"
                    tabindex="0" role="button" aria-controls="navLinks" aria-expanded="false">
                    <span></span>
                    <span></span>
                    <span></span>
                </label>

                <!-- Navigation Links -->
                <div class="khezana-nav-links" aria-label="التنقل الرئيسي" id="navLinks" role="navigation">
                    <a href="{{ route('public.items.index') }}" class="khezana-nav-link">
                        <svg class="khezana-nav-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            width="18" height="18" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        {{ __('common.ui.view_items') }}
                    </a>
                    <a href="{{ route('public.requests.index') }}" class="khezana-nav-link">
                        <svg class="khezana-nav-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            width="18" height="18" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ __('common.ui.view_requests') }}
                    </a>
                </div>

                <!-- Actions -->
                <div class="khezana-nav-actions">
                    @auth
                        <div class="khezana-user-dropdown">
                            <button class="khezana-user-trigger"
                                aria-label="{{ __('common.ui.user_menu') ?? 'قائمة المستخدم' }}" aria-expanded="false"
                                aria-haspopup="true" id="userMenuTrigger">
                                <span class="khezana-user-icon" aria-hidden="true">👤</span>
                                <span
                                    class="khezana-user-name-short">{{ Str::limit(Auth::user()->name ?? Auth::user()->phone, 15) }}</span>
                                <span class="khezana-dropdown-icon" aria-hidden="true">▼</span>
                            </button>
                            <div class="khezana-user-menu" role="menu" aria-labelledby="userMenuTrigger">
                                <a href="{{ route('profile.show') }}" class="khezana-user-menu-item" role="menuitem">
                                    {{ __('profile.title') }}
                                </a>
                                <a href="{{ route('items.index') }}" class="khezana-user-menu-item" role="menuitem">
                                    {{ __('common.ui.my_items') }}
                                </a>
                                <a href="{{ route('requests.index') }}" class="khezana-user-menu-item" role="menuitem">
                                    {{ __('common.ui.my_requests') }}
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="khezana-logout-form">
                                    @csrf
                                    <button type="submit" class="khezana-user-menu-item khezana-user-menu-item-logout"
                                        role="menuitem" aria-label="{{ __('common.ui.logout') }}">
                                        {{ __('common.ui.logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="khezana-btn khezana-btn-secondary khezana-btn-header">
                            <svg class="khezana-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                width="18" height="18" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            {{ __('common.ui.login') }}
                        </a>
                        <a href="{{ route('register') }}" class="khezana-btn khezana-btn-primary khezana-btn-header">
                            <svg class="khezana-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                width="18" height="18" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            {{ __('common.ui.register') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>
