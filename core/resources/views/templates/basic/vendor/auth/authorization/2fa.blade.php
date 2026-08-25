@extends($activeTemplate . 'layouts.auth')
@section('content')
    @php
        $sectionName = '2fa';
        $content = getContent($sectionName . '.content', true)?->data_values ?? null;
        $elements = getContent($sectionName . '.element', orderById: true);
    @endphp
    <h2 class="account-main__title">
        {{ __($content?->form_heading) }}
    </h2>
    <form action="{{ route('vendor.2fa.verify') }}" method="POST" class="account-form submit-form">
        @csrf
        <div class="form-group mb-0 mt-3">
            @include($activeTemplate . 'partials.frontend_verification_code')
        </div>

        <div class="form--group">
            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
        </div>
    </form>
@endsection
