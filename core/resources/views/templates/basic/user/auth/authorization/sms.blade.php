@extends($activeTemplate . 'layouts.auth')
@section('content')
    @php
        $sectionName = 'mobile_verification';
        $content = getContent($sectionName . '.content', true)?->data_values ?? null;
        $elements = getContent($sectionName . '.element', orderById: true);
    @endphp
    <h2 class="account-main__title">
        {{ __($content?->form_heading) }}
    </h2>
    <form action="{{ route('user.verify.mobile') }}" method="POST" class="account-form submit-form">
        @csrf
        <p class="verification-text">@lang('A 6 digit verification code sent to your mobile number') : +{{ showMobileNumber(auth()->user()->mobileNumber) }}</p>
        <div class="form-group mb-0 mt-3">
            @include($activeTemplate . 'partials.frontend_verification_code')
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
        </div>
        <div class="form-group">
            <p>
                @lang('If you don\'t get any code'),
                <span class="countdown-wrapper">
                    @lang('try again after')
                    <span id="countdown">--</span>
                    @lang('seconds').
                </span>
                <a href="{{ route('user.send.verify.code', 'sms') }}"
                    class="have-account__link text--base-two try-again-link d-none"> @lang('Try again').</a>
            </p>
            <p>
                <a class="have-account__link text--base-two" href="{{ route('user.logout') }}">@lang('Logout')</a>
            </p>
        </div>
    </form>
@endsection
@push('script')
    <script>
        "use strict";
        var distance = Number(
            "{{ isset($user->ver_code_send_at) ? $user->ver_code_send_at->addMinutes(2)->timestamp - time() : '' }}");
        var x = setInterval(function() {
            distance--;
            document.getElementById("countdown").innerHTML = distance;
            if (distance <= 0) {
                clearInterval(x);
                document.querySelector('.countdown-wrapper').classList.add('d-none');
                document.querySelector('.try-again-link').classList.remove('d-none');
            }
        }, 1000);
    </script>
@endpush
