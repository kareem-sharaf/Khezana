{{-- Profile Navigation --}}
<nav class="khezana-profile-nav" aria-label="{{ __('profile.navigation') }}">
    <ul class="khezana-profile-nav__list">
        <li class="khezana-profile-nav__item">
            <a 
                href="{{ route('profile.show') }}" 
                class="khezana-profile-nav__link {{ request()->routeIs('profile.show') ? 'khezana-profile-nav__link--active' : '' }}"
            >
                <span class="khezana-profile-nav__icon">👤</span>
                <span>{{ __('profile.overview') }}</span>
            </a>
        </li>
        <li class="khezana-profile-nav__item">
            <a 
                href="{{ route('profile.edit') }}" 
                class="khezana-profile-nav__link {{ request()->routeIs('profile.edit') ? 'khezana-profile-nav__link--active' : '' }}"
            >
                <span class="khezana-profile-nav__icon">✏️</span>
                <span>{{ __('profile.edit_profile') }}</span>
            </a>
        </li>
        <li class="khezana-profile-nav__item">
            <a 
                href="{{ route('profile.password') }}" 
                class="khezana-profile-nav__link {{ request()->routeIs('profile.password') ? 'khezana-profile-nav__link--active' : '' }}"
            >
                <span class="khezana-profile-nav__icon">🔒</span>
                <span>{{ __('profile.password.title') }}</span>
            </a>
        </li>
    </ul>
</nav>
