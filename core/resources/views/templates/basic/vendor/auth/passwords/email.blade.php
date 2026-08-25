@extends($activeTemplate . 'layouts.auth')
@section('content')
    @php
        $sectionName = 'forget_password';
        $content = getContent($sectionName . '.content', true)?->data_values ?? null;
        $elements = getContent($sectionName . '.element', orderById: true);
    @endphp

    <h2 class="account-main__title">
        {{ __($content?->form_heading) }}
    </h2>

    <form method="POST" action="{{ route('vendor.password.email') }}" class="verify-gcaptcha account-form ">
        @csrf
        <div class="form-group">
            <label class="form--label">@lang('Email or Username')</label>
            <input type="text" class="form--control" name="value" value="{{ old('value') }}" required autofocus="off">
        </div>

        <div class="col-sm-12">
            <x-captcha />
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
        </div>
    </form>
@endsection
