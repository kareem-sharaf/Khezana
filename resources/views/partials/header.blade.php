@php
    $categories = \App\Models\Category::active()->roots()->with('children')->orderBy('name')->get();
@endphp

<header class="site-header" role="banner">
    <div class="container">
        <nav class="navbar" role="navigation" aria-label="{{ trans('common.ui.main_navigation') }}">
            <div class="navbar__brand">
                <a href="{{ route('home') }}" class="navbar__logo">
                    {{ config('app.name', 'Khezana') }}
                </a>
            </div>
            
            <button class="navbar__toggle" 
                    aria-label="{{ trans('common.ui.menu') }}" 
                    aria-expanded="false"
                    onclick="document.querySelector('.navbar__menu').classList.toggle('is-open')">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <div class="navbar__menu">
                <ul class="navbar__nav">
                    <li class="navbar__item">
                        <a href="{{ route('public.items.index') }}" 
                           class="navbar__link {{ request()->routeIs('public.items.*') ? 'is-active' : '' }}">
                            {{ trans('items.plural') }}
                        </a>
                    </li>
                    <li class="navbar__item">
                        <a href="{{ route('public.requests.index') }}" 
                           class="navbar__link {{ request()->routeIs('public.requests.*') ? 'is-active' : '' }}">
                            {{ trans('requests.plural') }}
                        </a>
                    </li>
                    <li class="navbar__item navbar__item--dropdown">
                        <button class="navbar__link" 
                                aria-haspopup="true" 
                                aria-expanded="false"
                                onclick="this.nextElementSibling.classList.toggle('is-open')">
                            {{ trans('common.navigation.categories') }} ▼
                        </button>
                        <ul class="navbar__dropdown" role="menu">
                            @foreach($categories as $category)
                                <li class="navbar__dropdown-item">
                                    <a href="{{ route('public.items.index', ['category_id' => $category->id]) }}" 
                                       class="navbar__dropdown-link">
                                        {{ $category->name }}
                                    </a>
                                    @if($category->children->isNotEmpty())
                                        <ul class="navbar__dropdown-submenu">
                                            @foreach($category->children as $child)
                                                <li>
                                                    <a href="{{ route('public.items.index', ['category_id' => $child->id]) }}">
                                                        {{ $child->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
                
                <div class="navbar__actions">
                    @guest
                        <x-button type="ghost" href="{{ route('login') }}">{{ trans('common.ui.login') }}</x-button>
                        <x-button type="primary" href="{{ route('register') }}">{{ trans('common.ui.register') }}</x-button>
                    @else
                        <a href="{{ route('items.create') }}" 
                           class="btn btn-primary"
                           title="{{ trans('common.ui.add_item') }}">
                            {{ trans('common.ui.add_item') }}
                        </a>
                        <div class="navbar__user">
                            <button class="navbar__user-toggle" 
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    onclick="this.nextElementSibling.classList.toggle('is-open')">
                                <span class="navbar__user-name">{{ auth()->user()->name }}</span>
                                <span class="navbar__user-icon">👤</span>
                            </button>
                            <ul class="navbar__user-menu" role="menu">
                                <li>
                                    <a href="{{ route('dashboard') }}" class="navbar__user-link">
                                        {{ trans('common.ui.dashboard') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('items.index') }}" class="navbar__user-link">
                                        {{ trans('common.ui.my_items') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('requests.index') }}" class="navbar__user-link">
                                        {{ trans('common.ui.my_requests') }}
                                    </a>
                                </li>
                                <li class="navbar__user-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="navbar__user-link navbar__user-link--logout">
                                            {{ trans('common.ui.logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>
    </div>
</header>

