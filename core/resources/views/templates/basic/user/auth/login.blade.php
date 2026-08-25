@extends($activeTemplate . 'layouts.auth')
@section('content')
    @php
        $sectionName = 'login';
        $content = getContent($sectionName . '.content', true)?->data_values ?? null;
        $elements = getContent($sectionName . '.element', orderById: true);
        $isLoginOrRegister = 'login';
        $userType = 'user';
    @endphp
    <h2 class="account-main__title">{{ __($content?->form_heading) }}</h2>
    <form class="account-form verify-gcaptcha" method="POST" action="{{ route('user.login') }}">
        @csrf
        <div class="row">
            <div class="col-sm-12">
                @include($activeTemplate . 'partials.social_login')
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="username" class="form--label">@lang('Username or Email')</label>
                    <input type="text" class="form--control" name="username" value="{{ old('username') }}" id="username"
                        required>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <div class="d-flex justify-content-between">
                        <label for="password" class="form--label">@lang('Password')</label>
                    </div>
                    <div class="position-relative">
                        <input id="password" type="password" name="password" class="form-control form--control" required>
                        <span class="password-show-hide fas fa-eye toggle-password fa-eye-slash" id="#password"></span>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <x-captcha />
            </div>

            <div class="col-sm-12">
                <div class="form-group d-flex justify-content-between">
                    <div class="d-flex gap-2">
                        <input class="form--check align-self-start mt-1" name="remember" type="checkbox"
                            {{ old('remember') ? 'checked' : '' }} id="remember">
                        <div class="form-check-label">
                            <label for="remember"> @lang('Remember Me')</label>
                        </div>
                    </div>
                    <a class="have-account__link text--base-two" href="{{ route('user.password.request') }}">
                        @lang('Forgot your password?')
                    </a>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <button type="submit" id="recaptcha" class="btn btn--base w-100">@lang('Login')</button>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="have-account text-center">
                    <p class="have-account__text">
                        @lang('Don\'t have account?')
                        <a href="{{ route('user.register') }}"
                            class="have-account__link text--base-two">@lang('Register Now')</a>
                    </p>
                </div>
            </div>
        </div>
    </form>
@endsection
