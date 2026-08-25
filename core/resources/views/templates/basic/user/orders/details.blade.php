@extends($activeTemplate . 'layouts.master')

@section('content')
    @include($activeTemplate.'.partials.order_details', ['routeType' => 'user'])
@endsection
@push('breadcrumb-plugins')
    <a href="{{ route('user.order.index') }}" class="btn btn-outline--base-two">
        <i class="las la-angle-left"></i>
        @lang('Back to Orders')
    </a>
@endpush
