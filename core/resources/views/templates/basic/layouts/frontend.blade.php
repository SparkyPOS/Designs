@extends($activeTemplate . 'layouts.app')
@section('main')
    @if (!request()->routeIs('product.carts'))
        <a class="cart-btn @if (!count(getCartData())) d-none @endif" href="{{ route('product.carts') }}">
            <i class="fas fa-shopping-bag"></i>
            <span class="cart-btn__text">
                {{ count(getCartData()) }} @lang('Items')
            </span>
        </a>
    @endif

    @if (request()->routeIs('home'))
        @include($activeTemplate . '.partials.promotion')
    @endif
    @include($activeTemplate . '.partials.header')

    <main>
        @yield('content')
    </main>

    @include($activeTemplate . '.partials.footer')

    @php
        $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
    @endphp

    @if ($cookie->data_values->status == Status::ENABLE && !\Cookie::get('gdpr_cookie'))
        <!-- cookies dark version start -->
        <div class="cookies-card text-center hide">
            <div class="cookies-card__icon bg--base">
                <i class="las la-cookie-bite"></i>
            </div>
            <p class="mt-4 cookies-card__content">{{ $cookie->data_values->short_desc }} <a href="{{ route('cookie.policy') }}" target="_blank" class="text--primary">@lang('learn more')</a></p>
            <div class="cookies-card__btn mt-4">
                <a href="javascript:void(0)" class="btn btn--base w-100 policy">@lang('Allow')</a>
            </div>
        </div>
        <!-- cookies dark version end -->
    @endif
@endsection
@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/magnific-popup.css') }}">
@endpush
@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/jquery.magnific-popup.js') }}"></script>
@endpush
