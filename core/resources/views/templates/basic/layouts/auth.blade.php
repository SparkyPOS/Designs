@extends($activeTemplate . 'layouts.app')
@section('main')
    @if (isset($userType) &&
            request()->routeIs($userType . '.register') &&
            !gs($userType == 'user' ? 'registration' : 'vendor_registration'))
        @yield('content')
    @else
        <section class="account">
            <div class="account-main">
                <div class="account-main__header">
                    <a class="account-logo" href="{{ route('home') }}">
                        <img src="{{ siteLogo('dark') }}" alt="@lang('Logo')">
                    </a>
                    @if (($isLoginOrRegister ?? false) && ($userType ?? false))
                        <div class="btn-group btn-group--switch">
                            <a href="{{ route('user.' . $isLoginOrRegister) }}"
                                class="btn btn--sm btn-outline--base {{ $userType == 'user' ? 'active' : '' }}">@lang('Customer')</a>
                            <a href="{{ route('vendor.' . $isLoginOrRegister) }}"
                                class="btn btn--sm btn-outline--base {{ $userType == 'vendor' ? 'active' : '' }}">@lang('Vendor')</a>
                        </div>
                    @endif
                </div>
                <div class="account-main__body">
                    @yield('content')
                </div>
            </div>
            <div class="account-thumb bg-img"
                data-background-image="{{ frontendImage($sectionName, $content?->image, '1905x1295') }}">
                <div class="account-content">
                    <h1 class="account-content__title">
                        {{ __($content?->heading) }}
                    </h1>
                    <p class="account-content__text">
                        {{ __($content?->subheading) }}
                    </p>
                    <ul class="account-content__list">
                        @foreach ($elements as $item)
                            <li class="account-content__item">
                                <span class="account-content__icon">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span class="account-content__text">
                                    {{ __($item?->data_values?->list_item) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>
    @endif
@endsection
