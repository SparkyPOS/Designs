@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card custom--card rounded border">
                <div class="card-header">
                    <h3 class="card-title">@lang('Withdraw Via') {{ $withdraw->method->name }}</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert--base ">
                        <p class="mb-0"><i class="las la-info-circle"></i> @lang('You are requesting')
                            <b>{{ showAmount($withdraw->amount) }}</b> @lang('for withdraw.') @lang('The admin will send you')
                            <b class="text--success">{{ showAmount($withdraw->final_amount, currencyFormat: false) . ' ' . $withdraw->currency }}
                            </b> @lang('to your account.')
                        </p>
                    </div>
                    <form action="{{ route('vendor.withdraw.submit') }}" class="disableSubmission" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="mb-2">
                            @php
                                echo $withdraw->method->description;
                            @endphp
                        </div>
                        <x-viser-form identifier="id" identifierValue="{{ $withdraw->method->form_id }}" />
                        @if (auth('vendor')->user()->ts)
                            <div class="form-group">
                                <label>@lang('Google Authenticator Code')</label>
                                <input type="text" name="authenticator_code" class="form-control form--control" required>
                            </div>
                        @endif
                        <div class="form-group">
                            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('style')
    <style>
        .form-label {
            font-weight: 500;
            color: hsl(var(--heading-color));
        }
    </style>
@endpush
