@php
    $registrationDisabled = getContent('register_disable.content',true);
@endphp
<div class="register-disable">
    <div class="container">
        <div class="register-disable-image">
            <img class="fit-image" src="{{ frontendImage('register_disable',$registrationDisabled?->data_values?->image,'280x280') }}" alt="">
        </div>

        <h5 class="register-disable-title">{{ __($registrationDisabled?->data_values?->heading) }}</h5>
        <p class="register-disable-desc">
            {{ __($registrationDisabled?->data_values?->subheading) }}
        </p>
        <div class="text-center">
            <a href="{{ $registrationDisabled?->data_values?->button_url }}" class="btn btn--base"> <i class="las la-home"></i> {{ __($registrationDisabled?->data_values?->button_name) }}</a>
        </div>
    </div>
</div>
@push('style')
    <style>
        .register-disable {
            width: 100%;
            background-color: #fff;
            color: black;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100dvh;
        }

        .register-disable-image {
            max-width: 300px;
            width: 100%;
            margin: 0 auto 32px;
        }

        .register-disable-title {
            color: rgb(0 0 0 / 80%);
            font-size: 42px;
            margin-bottom: 18px;
            text-align: center
        }

        .register-disable-desc {
            color: rgb(0 0 0 / 50%);
            font-size: 18px;
            max-width: 565px;
            width: 100%;
            margin: 0 auto 32px;
            text-align: center;
        }
        .row:has(.register-disable) > * {
            width: 100% !important;
        }
        .container-fluid:has(.register-disable) {
            display: none;
        }
    </style>
@endpush
