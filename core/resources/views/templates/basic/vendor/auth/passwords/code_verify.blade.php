@extends($activeTemplate . 'layouts.auth')
@section('content')
    @php
        $sectionName = 'email_verification';
        $content = getContent($sectionName . '.content', true)?->data_values ?? null;
        $elements = getContent($sectionName . '.element', orderById: true);
    @endphp

    <h2 class="account-main__title">
        {{ __($content?->form_heading) }}
    </h2>
    <form action="{{ route('vendor.password.verify.code') }}" method="POST" class="account-form submit-form">
        @csrf
        <p class="verification-text">@lang('A 6 digit verification code sent to your email address') : {{ showEmailAddress($email) }}</p>
        <input type="hidden" name="email" value="{{ $email }}">
        <div class="form-group mb-0 mt-3">
            @include($activeTemplate . 'partials.frontend_verification_code')
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
        </div>
        <div class="form-group">
            @lang('Please check including your Junk/Spam Folder. if not found, you can')
            <a href="{{ route('vendor.password.request') }}"
                class="have-account__link text--base-two d-inline">@lang('Try to send again').</a>
        </div>
    </form>
@endsection
@push('style')
    <style>
        @media (min-width: 576px) {
            .verification-code-wrapper {
                padding: 0;
                width: 400px;
                border: 0;
            }
        }

        .verification-code-wrapper {
            margin-bottom: 20px;
        }
    </style>
@endpush
