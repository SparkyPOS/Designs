@php
    $text = isset($register) ? 'Register' : 'Login';
    $type = $userType ?? 'user';
@endphp
@if (gs('socialite_credentials')->google->status == Status::ENABLE)
    <div class="form-group continue-google">
        <a href="{{ route($type . '.social.login', 'google') }}" class="account-login">
            <div class="account-login__icon">
                <img src="{{ asset($activeTemplateTrue . 'images/google.svg') }}" alt="Google">
            </div>
            <div class="account-login__text">
                <span>
                    @lang("$text with Google")</span>
            </div>
        </a>
    </div>
@endif
@if (gs('socialite_credentials')->facebook->status == Status::ENABLE)
    <div class="form-group continue-facebook">
        <a href="{{ route($type . '.social.login', 'facebook') }}" class="account-login">
            <div class="account-login__icon">
                <img src="{{ asset($activeTemplateTrue . 'images/facebook.svg') }}" alt="Facebook">
            </div>
            <div class="account-login__text">
                <span>@lang("$text with Facebook")</span>
            </div>
        </a>
    </div>
@endif
@if (gs('socialite_credentials')->linkedin->status == Status::ENABLE)
    <div class="form-group continue-facebook">
        <a href="{{ route($type . '.social.login', 'linkedin') }}" class="account-login">
            <div class="account-login__icon">
                <img src="{{ asset($activeTemplateTrue . 'images/linkdin.svg') }}" alt="Linkedin">
            </div>
            <div class="account-login__text">
                <span>@lang("$text with Linkedin")</span>
            </div>
        </a>
    </div>
@endif

@if (gs('socialite_credentials')->linkedin->status ||
        gs('socialite_credentials')->facebook->status == Status::ENABLE ||
        gs('socialite_credentials')->google->status == Status::ENABLE)
    <div class="form-group or-login">
        <span class="or-login__text">@lang('Or') {{ __($text) }} @lang('With')</span>
    </div>
@endif
