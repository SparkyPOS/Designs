@extends($activeTemplate . 'layouts.master')

@section('content')
    @include($activeTemplate . '.partials.order_details', ['routeType' => 'vendor'])
    <div class="text-end mt-3">
        <a href="{{ route('vendor.order.print.invoice', $order->id) }}" target=blank class="btn btn--sm btn--dark m-1">
            <i class="fa fa-print"></i>
            @lang('Print')
        </a>
    </div>
@endsection
@push('breadcrumb-plugins')
    <a href="{{ route('vendor.order.index') }}" class="btn btn-outline--base-two">
        <i class="las la-angle-left"></i> @lang('Back to Orders')
    </a>
@endpush
