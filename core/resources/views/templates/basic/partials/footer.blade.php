@php
    $socialElements = getContent('social_icon.element', orderById: true)->pluck('data_values');
    $policyPages = getContent('policy_pages.element');
    $languages = App\Models\Language::get();
    $defaultLanguage = $languages->where('code', app()->getLocale())->first();
    $contact = getContent('contact_us.content', true)?->data_values ?? null;
    $footerContent = getContent('footer.content', true)?->data_values ?? null;
    $featuredCatalogCategories = App\Models\CatalogCategory::featured()->orderBy('id', 'DESC')->take(4)->get();
    $topCatalogs = App\Models\Catalog::active()
        ->withCount([
            'product as products_count' => function ($query) {
                $query->published();
            },
        ])
        ->orderByDesc('products_count')
        ->with('catalogCategory')
        ->take(4)
        ->get();
@endphp

<footer class="footer-area">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 mb-sm-5">
            <div class="footer-item">
                <div class="footer-item__logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ siteLogo() }}" alt="@lang('Logo')">
                    </a>
                </div>
            </div>
            <ul class="social-list">
                @foreach ($socialElements as $socialElement)
                    <li class="social-list__item">
                        <a href="{{ $socialElement->url ?? '' }}" target="_blank" class="social-list__link flex-center">
                            @php echo $socialElement->social_icon ?? ""; @endphp
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="row gy-4 justify-content-between">
            <div class="col-xl-2 col-md-3 col-sm-6 col-xsm-6">
                <div class="footer-item">
                    <h5 class="footer-item__title">@lang('Shortcut Links')</h5>
                    <ul class="footer-menu">
                        <li class="footer-menu__item">
                            <a href="{{ route('vendor.register') }}" class="footer-menu__link">
                                @lang('Vendor Registration')
                            </a>
                        </li>
                        <li class="footer-menu__item">
                            <a href="{{ route('user.register') }}" class="footer-menu__link">
                                @lang('Customer Registration')
                            </a>
                        </li>
                        <li class="footer-menu__item">
                            <a href="{{ route('vendor.login') }}" class="footer-menu__link">
                                @lang('Vendor Login')
                            </a>
                        </li>
                        <li class="footer-menu__item">
                            <a href="{{ route('user.login') }}" class="footer-menu__link">
                                @lang('Customer Login')
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-xl-2 col-md-3 col-sm-6 col-xsm-6">
                <div class="footer-item">
                    <h5 class="footer-item__title">@lang('Userful Link')</h5>
                    <ul class="footer-menu">
                        <li class="footer-menu__item">
                            <a href="{{ route('home') }}" class="footer-menu__link">
                                @lang('Home')
                            </a>
                        </li>
                        <li class="footer-menu__item">
                            <a href="{{ route('contact') }}" class="footer-menu__link">
                                @lang('Contact')
                            </a>
                        </li>
                        <li class="footer-menu__item">
                            <a href="{{ route('blog') }}" class="footer-menu__link">
                                @lang('Blog')
                            </a>
                        </li>
                        <li class="footer-menu__item">
                            <a href="{{ route('faq') }}" class="footer-menu__link">
                                @lang('FAQ')
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-xl-2 col-md-3 col-sm-6 col-xsm-6">
                <div class="footer-item">
                    <h5 class="footer-item__title">@lang('Featured Category')</h5>
                    <ul class="footer-menu">
                        @foreach ($featuredCatalogCategories as $category)
                            <li class="footer-menu__item">
                                <a href="{{ route('product.catalogs', $category->slug) }}" class="footer-menu__link">
                                    {{ __($category->name) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-xl-2 col-md-3 col-sm-6 col-xsm-6">
                <div class="footer-item">
                    <h5 class="footer-item__title">@lang('Top Catalogs')</h5>
                    <ul class="footer-menu">
                        @foreach ($topCatalogs as $catalog)
                            <li class="footer-menu__item">
                                <a href="{{ route('product.list', [$catalog?->catalogCategory?->slug, $catalog->slug]) }}" class="footer-menu__link">
                                    {{ __($catalog->name) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-xl-2">
                <div class="row gy-4 footer-area__contact text-xl-end">
                    <div class="col-xl-12 col-md-3 col-sm-6 col-xsm-6">
                        <div class="footer-item ">
                            <h5 class="footer-item__title">@lang('Contact Us')</h5>
                            <ul class="footer-menu">
                                <li class="footer-menu__item">
                                    <a href="tel:{{ $contact->contact_number ?? null }}" class="footer-menu__link">{{ $contact->contact_number ?? null }}</a>
                                </li>
                                <li class="footer-menu__item">
                                    <a href="mailto:{{ $contact->email_address ?? null }}" class="footer-menu__link">{{ $contact->email_address ?? null }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-12 col-md-3 col-sm-6 col-xsm-6">
                        <div class="footer-item">
                            <h5 class="footer-item__title">@lang('Location')</h5>
                            <ul class="footer-menu">
                                <li class="footer-menu__item">
                                    <a href="{{ $contact->map_link ?? 'javascript:void(0)' }}" class="footer-menu__link" target="_blank">{{ __($contact->contact_details ?? null) }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @if (gs('multi_language'))
                        <div class="col-xl-12 col-md-3 col-sm-6 col-xsm-6">
                            <div class="footer-item">
                                <h5 class="footer-item__title">@lang('Languages')</h5>
                                <div class="footer-item__location gap-3">
                                    @foreach ($languages ?? [] as $language)
                                        <div class="footer-item__location-item">
                                            <a href="{{ route('lang', $language->code) }}" class="{{ $defaultLanguage->code == $language->code ? 'active' : '' }}">{{ __(ucwords($language->code)) }}</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Footer Top End-->
    </div>
    <!-- bottom Footer -->
    <div class="bottom-footer">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div class="bottom-footer-text">
                    @lang('Copyright') &copy; {{ date('Y') }}
                    <a href="{{ route('home') }}" class="text--base">{{ gs('site_name') }}</a>. @lang('All Rights Reserved.')
                </div>
                <ul class="footer-menu">
                    @foreach ($policyPages ?? [] as $policyPage)
                        <li class="footer-menu__item">
                            <a href="{{ route('policy.pages', $policyPage?->slug) }}" class="footer-menu__link">{{ __($policyPage?->data_values?->title ?? '') }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</footer>
