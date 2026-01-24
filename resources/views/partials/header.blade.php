<!-- Header -->
<header class="khezana-header">
    <nav class="khezana-nav">
        <div class="khezana-container">
            <div class="khezana-nav-content">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="khezana-logo" aria-label="{{ __('common.ui.main_navigation') }}">
                    @if(file_exists(public_path('logo.svg')))
                        <img 
                            src="{{ asset('logo.svg') }}" 
                            alt="{{ config('app.name') }}" 
                            class="khezana-logo-img"
                            loading="eager"
                            width="180"
                            height="45">
                        <span class="khezana-logo-text">خزانة</span>
                    @elseif(file_exists(public_path('logo.png')))
                        <img 
                            src="{{ asset('logo.png') }}" 
                            alt="{{ config('app.name') }}" 
                            class="khezana-logo-img"
                            loading="eager"
                            width="180"
                            height="45">
                        <span class="khezana-logo-text">خزانة</span>
                    @else
                        <span class="khezana-logo-text">{{ config('app.name') }}</span>
                    @endif
                </a>

                <!-- Mobile Menu Toggle -->
                <input type="checkbox" id="mobileMenuToggle" class="khezana-mobile-menu-checkbox" tabindex="-1">
                <label for="mobileMenuToggle" class="khezana-mobile-menu-toggle" aria-label="{{ __('common.ui.menu') }}" tabindex="0" role="button" aria-controls="navLinks" aria-expanded="false">
                    <span></span>
                    <span></span>
                    <span></span>
                </label>

                <!-- Navigation Links -->
                <div class="khezana-nav-links" aria-label="التنقل الرئيسي" id="navLinks" role="navigation">
                    <!-- Items Dropdown -->
                    <div class="khezana-nav-dropdown">
                        <a 
                            href="{{ route('public.items.index') }}" 
                            class="khezana-nav-link khezana-nav-link-dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                            id="itemsDropdownTrigger">
                            {{ __('items.title') }}
                            <span class="khezana-dropdown-icon" aria-hidden="true">▼</span>
                        </a>
                        <div class="khezana-dropdown-menu" role="menu" aria-labelledby="itemsDropdownTrigger">
                            <a href="{{ route('public.items.index', ['operation_type' => 'sell']) }}" class="khezana-dropdown-item" role="menuitem">
                                {{ __('items.operation_types.sell') }}
                            </a>
                            <a href="{{ route('public.items.index', ['operation_type' => 'rent']) }}" class="khezana-dropdown-item" role="menuitem">
                                {{ __('items.operation_types.rent') }}
                            </a>
                            <a href="{{ route('public.items.index', ['operation_type' => 'donate']) }}" class="khezana-dropdown-item" role="menuitem">
                                {{ __('items.operation_types.donate') }}
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('public.requests.index') }}" class="khezana-nav-link">
                        {{ __('requests.title') }}
                    </a>
                    @auth
                        <a href="{{ route('items.index') }}" class="khezana-nav-link">
                            {{ __('common.ui.my_items') }}
                        </a>
                        <a href="{{ route('requests.index') }}" class="khezana-nav-link">
                            {{ __('common.ui.my_requests') }}
                        </a>
                    @endauth
                </div>

                <!-- Actions -->
                <div class="khezana-nav-actions">
                    @auth
                        <a href="{{ route('items.create') }}" class="khezana-btn khezana-btn-primary khezana-btn-cta" tabindex="0">
                            {{ __('common.ui.add_item') }}
                        </a>
                        <div class="khezana-user-dropdown">
                            <button 
                                class="khezana-user-trigger" 
                                aria-label="{{ __('common.ui.user_menu') ?? 'قائمة المستخدم' }}" 
                                aria-expanded="false"
                                aria-haspopup="true"
                                id="userMenuTrigger">
                                <span class="khezana-user-icon" aria-hidden="true">👤</span>
                                <span class="khezana-user-name-short">{{ Str::limit(Auth::user()->name ?? Auth::user()->phone, 15) }}</span>
                                <span class="khezana-dropdown-icon" aria-hidden="true">▼</span>
                            </button>
                            <div class="khezana-user-menu" role="menu" aria-labelledby="userMenuTrigger">
                                <a href="{{ route('profile.show') }}" class="khezana-user-menu-item" role="menuitem">
                                    {{ __('profile.title') }}
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="khezana-logout-form">
                                    @csrf
                                    <button 
                                        type="submit" 
                                        class="khezana-user-menu-item khezana-user-menu-item-logout"
                                        role="menuitem"
                                        aria-label="{{ __('common.ui.logout') }}">
                                        {{ __('common.ui.logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="khezana-btn khezana-btn-secondary">
                            {{ __('common.ui.login') }}
                        </a>
                        <a href="{{ route('register') }}" class="khezana-btn khezana-btn-secondary">
                            {{ __('common.ui.register') }}
                        </a>
                        <a href="{{ route('items.create') }}" class="khezana-btn khezana-btn-primary khezana-btn-cta" tabindex="0">
                            {{ __('common.ui.add_item') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>
