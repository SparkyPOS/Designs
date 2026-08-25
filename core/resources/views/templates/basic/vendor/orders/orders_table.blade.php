<div class="table-responsive--md table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>@lang('Order Number')</th>
                <th class="text-center">@lang('Items')</th>
                <th>@lang('Payment')</th>
                <th>@lang('Status')</th>
                <th>@lang('Action')</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>#{{ $order->order_number }}</td>
                    <td>{{ $order->orderDetail->sum('quantity') }}</td>
                    <td>@php echo $order->paymentBadge() @endphp</td>
                    <td>@php echo $order->statusBadge() @endphp </td>
                    <td>

                        @php
                            $question = null;
                            $canCancel = false;
                            $disabled = false;
                            $isPaid = $order->payment_status == Status::PAYMENT_SUCCESS;

                            if (!$isPaid) {
                                $disabled = true;
                                $buttonText = 'Awaiting Payment';
                            } elseif ($order->status == Status::ORDER_PENDING) {
                                $canCancel = true;
                                $buttonText = 'Processing';
                                $question = 'Are you sure to mark the order as processing?';
                            } elseif ($order->status == Status::ORDER_PROCESSING) {
                                $canCancel = true;
                                $buttonText = 'Dispatch';
                                $question = 'Are you sure to mark the order as dispatched?';
                            } elseif ($order->status == Status::ORDER_DISPATCHED) {
                                $buttonText = 'Deliver';
                                $question = 'Are you sure to mark the order as delivered?';
                            } elseif ($order->status == Status::ORDER_DELIVERED) {
                                $disabled = true;
                                $buttonText = 'Deliver';
                                $question = null;
                            } elseif ($order->status == Status::ORDER_CANCELED) {
                                $disabled = true;
                                $buttonText = 'Canceled';
                                $question = null;
                            } elseif ($order->status == Status::ORDER_PAYMENT_RETURNED) {
                                $disabled = true;
                                $buttonText = 'Payment Returned';
                                $question = null;
                            }
                        @endphp
                        <div class="dropdown table-dropdown">
                            <button class="edit-btn dropdown-toggle ms-auto " type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('vendor.order.details', $order->order_number) }}"
                                        class="dropdown-item"> <i class="me-1 las la-desktop"></i>
                                        @lang('View')</a>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item confirmationBtn"
                                        data-question="{{ __($question) }}"
                                        data-action="{{ route('vendor.order.status.change', $order->id) }}"
                                        @disabled($disabled)><i class="me-1 la la-check"></i> {{ __($buttonText) }}</button>
                                </li>
                                <li>
                                    @if (!$isPaid)
                                        <button class="dropdown-item" type="button" disabled>
                                            <i class="me-1 las la-clock"></i>@lang('Payment Required')
                                        </button>
                                    @elseif (Route::is('vendor.order.dispatched'))
                                        <button class="dropdown-item confirmationBtn"
                                            data-question="@lang('Are you sure to change status as returned?')"
                                            data-action="{{ route('vendor.order.return', $order->id) }}">
                                            <i class="me-1 las la-undo-alt"> </i>@lang('Return')
                                        </button>
                                    @else
                                        <button class="dropdown-item confirmationBtn"
                                            data-question="@lang('Are you sure to cancel this order?')"
                                            data-action="{{ route('vendor.order.status.cancel', $order->id) }}"
                                            @disabled(!$canCancel)>
                                            <i class="me-1 la la-ban"></i> @lang('Cancel')
                                        </button>
                                    @endif
                                </li>
                            </ul>
                        </div>
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
