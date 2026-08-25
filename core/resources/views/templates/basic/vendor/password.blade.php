@extends($activeTemplate . 'layouts.master')
@section('content')
    <section class="settings-container">
        <div class="row">
            <div class="col-xxxl-3 col-xxl-4">
                @include($activeTemplate . '.partials.vendor_account_menu')
            </div>
            <div class="col-xxxl-9 col-xxl-8">
                <div class="setting-content">
                    <div class="setting-content__details">
                        <form method="post">
                            @csrf
                            <div class="form-group">
                                <label class="form--label">@lang('Current Password')</label>
                                <input type="password" class="form--control" name="current_password" required
                                    autocomplete="current-password">
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Password')</label>
                                <input type="password"
                                    class="form--control @if (gs('secure_password')) secure-password @endif"
                                    name="password" required autocomplete="current-password">
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Confirm Password')</label>
                                <input type="password" class="form--control" name="password_confirmation" required autocomplete="current-password">
                            </div>
                            <div>
                                <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
