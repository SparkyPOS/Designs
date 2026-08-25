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
                        <div class="row gy-4">
                            @if (!authVendor()->ts)
                                <div class="col-md-6">
                                    <div class="card custom--card">
                                        <div class="card-header">
                                            <h3 class="card-title">@lang('Add Your Account')</h3>
                                        </div>

                                        <div class="card-body">
                                            <h6 class="mb-3">
                                                @lang('Use the QR code or setup key on your Google Authenticator app to add your account.')
                                            </h6>
                                            <div class="form-group mx-auto text-center">
                                                <img class="mx-auto" src="{{ $qrCodeUrl }}" alt="QR">
                                            </div>
                                            <div class="form-group">
                                                <label class="form--label">@lang('Setup Key')</label>
                                                <div class="input-group">
                                                    <input type="text" name="key" value="{{ $secret }}"
                                                        class="form-control form--control referralURL" readonly>
                                                    <button type="button" class="input-group-text copytext" id="copyBoard">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <label><i class="fas fa-info-circle"></i> @lang('Help')</label>
                                            <p>@lang('Google Authenticator is a multifactor app for mobile devices. It generates timed codes used during the 2-step verification process. To use Google Authenticator, install the Google Authenticator application on your mobile device.') <a class="text--base-two"
                                                    href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en"
                                                    target="_blank">@lang('Download')</a></p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-6">
                                @if (authVendor()->ts)
                                    <div class="card custom--card">
                                        <div class="card-header">
                                            <h3 class="card-title">@lang('Disable 2FA Security')</h3>
                                        </div>
                                        <form action="{{ route('vendor.twofactor.disable') }}" method="POST">
                                            <div class="card-body">
                                                @csrf
                                                <input type="hidden" name="key" value="{{ $secret }}">
                                                <div class="form-group">
                                                    <label class="form--label">@lang('Google Authenticator OTP')</label>
                                                    <input type="text" class="form--control" name="code" required>
                                                </div>
                                                <button type="submit"
                                                    class="btn btn--base w-100">@lang('Submit')</button>
                                            </div>
                                        </form>
                                    </div>
                                @else
                                    <div class="card custom--card">
                                        <div class="card-header">
                                            <h3 class="card-title">@lang('Enable 2FA Security')</h3>
                                        </div>
                                        <form action="{{ route('vendor.twofactor.enable') }}" method="POST">
                                            <div class="card-body">
                                                @csrf
                                                <input type="hidden" name="key" value="{{ $secret }}">
                                                <div class="form-group">
                                                    <label class="form--label">@lang('Google Authenticator OTP')</label>
                                                    <input type="text" class="form--control" name="code" required>
                                                </div>
                                                <button type="submit"
                                                    class="btn btn--base w-100">@lang('Submit')</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .copied::after {
            background-color: #{{ gs('base_color') }};
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('#copyBoard').on('click', function() {
                var copyText = document.getElementsByClassName("referralURL");
                copyText = copyText[0];
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                /*For mobile devices*/
                document.execCommand("copy");
                copyText.blur();
                this.classList.add('copied');
                setTimeout(() => this.classList.remove('copied'), 1500);
            });
        })(jQuery);
    </script>
@endpush
