@extends($activeTemplate . 'layouts.auth')
@section('content')
    @php
        $sectionName = 'register';
        $content = null;
        $elements = [];
        $isLoginOrRegister = 'register';
        $userType = 'vendor';
    @endphp
    @if (gs('vendor_registration'))
        @php
            $sectionName = 'register';
            $content = getContent($sectionName . '.content', true)?->data_values ?? null;
            $elements = getContent($sectionName . '.element', orderById: true);
            $isLoginOrRegister = 'register';
            $userType = 'vendor';
        @endphp

        <h2 class="account-main__title">
            {{ __($content?->form_heading) }}
        </h2>
        <form class="account-form verify-gcaptcha disableSubmission" action="{{ route('vendor.register') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-sm-12">
                    @include($activeTemplate . 'partials.social_login', ['register' => true])
                </div>
                @if (session()->get('reference') != null)
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="referenceBy" class="form--label">@lang('Reference by')</label>
                            <input type="text" name="referBy" id="referenceBy" class="form--control"
                                value="{{ session()->get('reference') }}" readonly>
                        </div>
                    </div>
                @endif
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form--label">@lang('First Name')</label>
                        <input type="text" class="form--control" name="firstname" value="{{ old('firstname') }}"
                            required>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form--label">@lang('Last Name')</label>
                        <input type="text" class="form--control" name="lastname" value="{{ old('lastname') }}" required>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="form--label">@lang('E-Mail Address')</label>
                        <input type="email" class="form--control checkUser" name="email" value="{{ old('email') }}"
                            required>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form--label">@lang('Password')</label>
                        <div class="position-relative">
                            <input type="password" name="password"
                                class="form--control @if (gs('secure_password')) secure-password @endif"
                                value="" id="password" required>
                            <span class="password-show-hide fas fa-eye toggle-password fa-eye-slash" id="#password"></span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form--label">@lang('Confirm Password')</label>
                        <div class="position-relative">
                            <input type="password" class="form--control" id="password_confirmation"
                                name="password_confirmation" required>
                            <div class="password-show-hide fas fa-eye toggle-password fa-eye-slash"
                                id="#password_confirmation">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <x-captcha />
                </div>

                @if (gs('agree'))
                    @php
                        $policyPages = getContent('policy_pages.element', false, orderById: true);
                    @endphp
                    <div class="col-sm-12">
                        <div class="form-group d-flex gap-2">
                            <input class="form--check align-self-start mt-1" type="checkbox" id="agree"
                                @checked(old('agree')) name="agree" required>
                            <div class="form-check-label ">
                                <label class="" for="agree"> @lang('I agree with')</label>
                                @foreach ($policyPages as $policy)
                                    <a href="{{ route('policy.pages', $policy->slug) }}" target="_blank"
                                        class="text--base-two">{{ __($policy->data_values->title) }}</a>
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-sm-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn--base w-100">@lang('Sign Up')</button>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="have-account text-center">
                        <p class="have-account__text">
                            @lang('Already Have An Account?')
                            <a href="{{ route('vendor.login') }}"
                                class="have-account__link text--base-two">@lang('Login')</a>
                        </p>
                    </div>
                </div>
            </div>
        </form>
        <div class="modal fade" id="existModalCenter" tabindex="-1" role="dialog" aria-labelledby="existModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                            <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="las la-times"></i>
                            </span>
                    </div>
                    <div class="modal-body">
                        <h6 class="text-center mb-0">@lang('You already have an account please Login ')</h6>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark btn--sm"
                            data-bs-dismiss="modal">@lang('Close')</button>
                        <a href="{{ route('vendor.login') }}" class="btn btn--base btn--sm">@lang('Login')</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        @include($activeTemplate . 'partials.registration_disabled')
    @endif

@endsection
@if (gs('vendor_registration'))
    @if (gs('secure_password'))
        @push('script-lib')
            <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
        @endpush
    @endif

    @push('script')
        <script>
            "use strict";
            (function($) {

                $('.checkUser').on('focusout', function(e) {
                    var url = '{{ route('vendor.checkUser') }}';
                    var value = $(this).val();
                    var token = '{{ csrf_token() }}';

                    var data = {
                        email: value,
                        _token: token
                    }

                    $.post(url, data, function(response) {
                        if (response.data != false) {
                            $('#existModalCenter').modal('show');
                        }
                    });
                });
            })(jQuery);
        </script>
    @endpush

@endif
