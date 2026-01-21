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
                    <a href="{{ route('public.items.index') }}" class="khezana-nav-link">
                        {{ __('common.ui.offers') }}
                    </a>
                    <a href="{{ route('public.items.index', ['operation_type' => 'sell']) }}" class="khezana-nav-link">
                        {{ __('items.operation_types.sell') }}
                    </a>
                    <a href="{{ route('public.items.index', ['operation_type' => 'rent']) }}" class="khezana-nav-link">
                        {{ __('items.operation_types.rent') }}
                    </a>
                    <a href="{{ route('public.items.index', ['operation_type' => 'donate']) }}"
                        class="khezana-nav-link">
                        {{ __('items.operation_types.donate') }}
                    </a>
                    <a href="{{ route('public.requests.index') }}" class="khezana-nav-link">
                        {{ __('requests.title') }}
                    </a>
                </div>

                <!-- Actions -->
                <div class="khezana-nav-actions">
                    @auth
                        <div class="khezana-user-box" aria-label="حساب المستخدم">
                            <div class="khezana-user-info">
                                <span class="khezana-user-greeting">مرحباً،</span>
                                <span class="khezana-user-name">{{ Auth::user()->name ?? Auth::user()->phone }}</span>
                            </div>
                            <div class="khezana-user-actions">
                                <a href="{{ route('items.index') }}" class="khezana-btn khezana-btn-secondary">
                                    {{ __('common.ui.my_items') }}
                                </a>
                                <a href="{{ route('requests.index') }}" class="khezana-btn khezana-btn-secondary">
                                    {{ __('common.ui.my_requests') }}
                                </a>
                                <a href="{{ route('items.create') }}" class="khezana-btn khezana-btn-primary">
                                    {{ __('common.ui.add_item') }}
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="khezana-logout-form">
                                    @csrf
                                    <button type="submit" class="khezana-btn khezana-btn-ghost">
                                        {{ __('common.ui.logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="khezana-guest-actions">
                            <a href="{{ route('login') }}" class="khezana-btn khezana-btn-secondary">
                                {{ __('common.ui.login') }}
                            </a>
                            <a href="{{ route('register') }}" class="khezana-btn khezana-btn-primary">
                                {{ __('common.ui.register') }}
                            </a>
                            <a href="{{ route('items.create') }}"
                                class="khezana-btn khezana-btn-primary khezana-btn-outline">
                                {{ __('common.ui.add_item') }}
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>
