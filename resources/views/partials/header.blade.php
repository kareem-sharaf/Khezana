<!-- Header -->
<header class="khezana-header">
    <nav class="khezana-nav">
        <div class="khezana-container">
            <div class="khezana-nav-content">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="khezana-logo" aria-label="{{ __('common.ui.main_navigation') }}">
                    <span class="khezana-logo-text">{{ config('app.name') }}</span>
                </a>

                <!-- Navigation Links -->
                <div class="khezana-nav-links" aria-label="التنقل الرئيسي">
                    <!-- Offers Dropdown -->
                    <div class="khezana-nav-dropdown">
                        <a href="{{ route('public.items.index') }}" class="khezana-nav-link khezana-nav-link-dropdown">
                            {{ __('common.ui.offers') }}
                            <span class="khezana-dropdown-icon">▼</span>
                        </a>
                        <div class="khezana-dropdown-menu">
                            <a href="{{ route('public.items.index', ['operation_type' => 'sell']) }}" class="khezana-dropdown-item">
                                {{ __('items.operation_types.sell') }}
                            </a>
                            <a href="{{ route('public.items.index', ['operation_type' => 'rent']) }}" class="khezana-dropdown-item">
                                {{ __('items.operation_types.rent') }}
                            </a>
                            <a href="{{ route('public.items.index', ['operation_type' => 'donate']) }}" class="khezana-dropdown-item">
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
                        <a href="{{ route('items.create') }}" class="khezana-btn khezana-btn-primary khezana-btn-cta">
                            {{ __('common.ui.add_item') }}
                        </a>
                        <div class="khezana-user-dropdown">
                            <button class="khezana-user-trigger" aria-label="حساب المستخدم" aria-expanded="false">
                                <span class="khezana-user-icon">👤</span>
                                <span class="khezana-user-name-short">{{ Str::limit(Auth::user()->name ?? Auth::user()->phone, 15) }}</span>
                                <span class="khezana-dropdown-icon">▼</span>
                            </button>
                            <div class="khezana-user-menu">
                                <form method="POST" action="{{ route('logout') }}" class="khezana-logout-form">
                                    @csrf
                                    <button type="submit" class="khezana-user-menu-item khezana-user-menu-item-logout">
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
                        <a href="{{ route('items.create') }}" class="khezana-btn khezana-btn-primary khezana-btn-cta">
                            {{ __('common.ui.add_item') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>
