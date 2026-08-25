<div class="table-responsive--md table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th class="text-left">@lang('Order Number')</th>
                <th class="text-center">@lang('Items')</th>
                <th class="text-center">@lang('Payment')</th>
                <th class="text-center">@lang('Status')</th>
                <th class="text-right">@lang('Action')</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td data-label="@lang('Order Number')">#{{ $order->order_number }}</td>
                    <td data-label="@lang('Items')">{{ $order->orderDetail->sum('quantity') }}</td>
                    <td data-label="@lang('Payment')">@php echo $order->paymentBadge() @endphp</td>
                    <td data-label="@lang('Order')">@php echo $order->statusBadge() @endphp </td>
                    <td data-label="@lang('Order Number')">
                        @if ($order->payment_status != Status::PAYMENT_SUCCESS && $order->status == Status::ORDER_PENDING)
                            <a href="{{ route('user.deposit.index', $order->order_number) }}"
                                class="btn btn--base btn--xs">
                                <i class="las la-credit-card"></i> @lang('Pay Now')
                            </a>
                        @endif
                        <a href="{{ route('user.order.details', $order->order_number) }}"
                            class="btn btn-outline--base-two btn--xs"> <i class="las la-desktop"></i>
                            @lang('View')</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-muted text-center" colspan="100%">@lang('No order yet')</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
