@php
    $pages = App\Models\Page::where('is_default', Status::NO)
        ->where('tempname', activeTemplate())
        ->orderBy('id', 'DESC')
        ->get();
    $featuredCategories = App\Models\CatalogCategory::active()->featured()->get();
    $megaMenuCategories = App\Models\CatalogCategory::active()
        ->megaMenu()
        ->whereHas('catalogs')
        ->with([
            'catalogs' => function ($query) {
                $query->active();
            },
        ])
        ->get();
    $catalogCategories = App\Models\CatalogCategory::active()->general()->get();
@endphp
<header class="header" id="header">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <button class="navbar-toggler header-button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasHeader"
                aria-controls="offcanvasHeader">
                <span id="hiddenNav"><i class="las la-bars"></i></span>
            </button>
            <a class="navbar-brand logo me-auto" href="{{ route('home') }}">
                <img src="{{ siteLogo('dark') }}" alt="@lang('Logo')">
            </a>
            <div class="offcanvas navbar--offcanvas offcanvas-start" tabindex="-1" id="offcanvasHeader">
                <div class="offcanvas-header">
                    <a class="navbar-brand logo me-auto" href="{{ route('home') }}">
                        <img src="{{ siteLogo('dark') }}" alt="@lang('Image')">
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                @php
                    $isVendor = auth()->guard('vendor')->check();
                    $isUser = auth()->check();
                @endphp

                <ul class="navbar-nav nav-menu align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link {{ menuActive('home') }}" aria-current="page" href="{{ route('home') }}">
                            <span class="nav-link__text">@lang('Home')</span>
                        </a>
                    </li>

                    @foreach ($pages as $k => $data)
                        <li class="nav-item">
                            <a class="nav-link {{ menuActive('pages', null, $data->slug) }}"
                                href="{{ route('pages', [$data->slug]) }}">
                                <span class="nav-link__text">{{ __($data->name) }}</span>
                            </a>
                        </li>
                    @endforeach

                    <li class="nav-item">
                        <a class="nav-link dropdown-btn collapse-hover {{ menuActive('product.*') }}"
                            href="#dropdownHeader" data-bs-toggle="collapse" aria-expanded="false"
                            aria-controls="dropdownHeader">
                            <span class="nav-link__text">
                                @lang('Catalog')
                                <span class="nav-item__icon">
                                    <i class="fa-solid fa-caret-down"></i>
                                </span>
                            </span>
                        </a>
                        <div class="mega-menu">
                            <div class="mega-menu__content sidebar-submenu collapse" id="dropdownHeader">
                                <div class="mega-menu__featured">
                                    @foreach ($featuredCategories as $category)
                                        <a href="{{ route('product.catalogs', $category->slug) }}"
                                            class="mega-menu__featured-item">
                                            <h3 class="mega-menu__featured-title">{{ __($category->name) }}</h3>
                                            <div class="mega-menu__featured-thumb">
                                                <img src="{{ getImage(getFilePath('catalogCategory') . '/' . $category->image) }}"
                                                    alt="@lang('featured')">
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                                <div class="mega-menu__list">
                                    <div class="mega-menu__list-item header-category-list">
                                        <a href="{{ route('product.all.catalogs') }}" class="category">
                                            @lang('All Catalogs')
                                        </a>
                                        @foreach ($catalogCategories as $category)
                                            <a href="{{ route('product.catalogs', $category->slug) }}"
                                                class="category">
                                                {{ __($category->name) }}
                                            </a>
                                        @endforeach
                                    </div>

                                    @foreach ($megaMenuCategories as $category)
                                        <div class="mega-menu__list-item category-dropdown active">
                                            <div class="mega-menu__list-item-title dropdown-button">
                                                <a href="{{ route('product.catalogs', $category->slug) }}"
                                                    class="category">
                                                    {{ __($category->name) }}
                                                </a>
                                            </div>
                                            <div class="mega-menu__list-item-menu sidebar-submenu">
                                                @foreach ($category->catalogs as $catalog)
                                                    <a href="{{ route('product.list', [$category->slug, $catalog->slug]) }}"
                                                        class="menu-item">
                                                        {{ __($catalog->name) }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ menuActive('faq') }}" href="{{ route('faq') }}">
                            <span class="nav-link__text">@lang('FAQs')</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ menuActive('blog*') }}" href="{{ route('blog') }}">
                            <span class="nav-link__text">@lang('Blog')</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ menuActive('contact') }}" href="{{ route('contact') }}">
                            <span class="nav-link__text">@lang('Contact')</span>
                        </a>
                    </li>
                </ul>
            </div>
            <ul class="account-list d-flex gap-2">
                @if ($isVendor)
                    <li class="account-list__item">
                        <a href="{{ route('vendor.home') }}" class="btn btn--base">
                            @lang('Dashboard')
                        </a>
                    </li>
                @elseif($isUser)
                    <li class="account-list__item">
                        <a href="{{ route('user.home') }}" class="btn btn--base">
                            @lang('Dashboard')
                        </a>
                    </li>
                @else
                    <li class="account-list__item">
                        <a href="{{ route('user.login') }}" class="btn btn-outline--dark">
                            @lang('Login')
                        </a>
                    </li>
                    <li class="account-list__item">
                        <a href="{{ route('user.register') }}" class="btn btn--base">
                            @lang('Sign Up')
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</header>
