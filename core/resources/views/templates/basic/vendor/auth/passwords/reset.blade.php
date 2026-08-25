@extends($activeTemplate . 'layouts.auth')
@section('content')
    @php
        $sectionName = 'reset_password';
        $content = getContent($sectionName . '.content', true)?->data_values ?? null;
        $elements = getContent($sectionName . '.element', orderById: true);
    @endphp
    <h2 class="account-main__title">
        {{ __($content?->form_heading) }}
    </h2>
    <form method="POST" action="{{ route('vendor.password.update') }}" class="account-form">
        @csrf
        <div class="mb-4">
            <p>@lang('Your account is verified successfully. Now you can change your password. Please enter a strong password and don\'t share it with anyone.')</p>
        </div>
        <input type="hidden" name="email" value="{{ $email }}">
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-group">
            <label class="form--label">@lang('Password')</label>
            <div class="position-relative">
                <input type="password" class="form--control @if (gs('secure_password')) secure-password @endif"
                    name="password" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form--label">@lang('Confirm Password')</label>
            <div class="position-relative">
                <input type="password" class="form--control" name="password_confirmation" required>
            </div>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn--base w-100"> @lang('Submit')</button>
        </div>
    </form>
@endsection

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
