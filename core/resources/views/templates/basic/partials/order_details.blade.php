<div class="order-details">
    <div class="order-details-top justify-content-between">
        <div class="mb-3">
            <h3 class="order-details-id mb-0 d-flex align-items-center flex-wrap gap-3">
                <span class="order-details-id">#{{ $order->order_number }}</span>
                @php echo $order->paymentBadge() @endphp
                @php echo $order->statusBadge() @endphp
            </h3>
            <span> {{ showDateTime($order->created_at, 'd F, Y') }}</span>
            @if ($routeType == 'user' && $order->payment_status != Status::PAYMENT_SUCCESS && $order->status == Status::ORDER_PENDING)
                <a href="{{ route('user.deposit.index', $order->order_number) }}" class="btn btn--base btn--sm mt-2">
                    <i class="las la-credit-card"></i> @lang('Complete Payment')
                </a>
            @endif
        </div>
    </div>

    <div class="order-details-products mb-3">
        <div class="table-responsive table-responsive--md">
            <table class="table">
                <thead>
                    <tr>
                        <th class="border-0 text-start">@lang('Product')</th>
                        <th class="text-center border-0">@lang('Price')</th>
                        <th class="text-center border-0">@lang('Quantity')</th>
                        <th class="text-center border-0">@lang('Total Price')</th>
                        <th class="text-end border-0">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderDetail as $data)
                        <tr>
                            <td data-label="@lang('Product')">
                                <div class="single-product-item d-flex flex-column align-items-end flex-md-row align-items-md-start gap-2">
                                    <div class="thumb">
                                        <img class="lazyload" src="{{ $data->product->mainImage(true) }}" data-src="{{ $data->product->mainImage() }}" alt="@lang('image')">
                                    </div>

                                    <div class="content d-flex flex-column">
                                        {{ __($data?->product?->name) }}

                                        @if ($data->productVariant)
                                            - {{ __($data->productVariant?->name) }}
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td data-label="@lang('Price')"> {{ showAmount($data->price) }}</td>
                            <td data-label="@lang('Quantity')">{{ $data->quantity }}</td>
                            <td data-label="@lang('Total Price')">
                                {{ showAmount($data->price * $data->quantity) }}</td>
                            <td data-label="@lang('Action')">
                                <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
                                    <a class="btn btn--xs btn-outline--base-two text-nowrap" href="{{ route($routeType . '.order.print.area', [$order->order_number, $data->id]) }}">
                                        <i class="la la-print"></i>
                                        <span class="d-none d-md-inline-block">@lang('Print Area')</span>
                                    </a>
                                    @if ($order->status == Status::ORDER_DELIVERED && request()->routeIs('user.order.details'))
                                        <a class="btn btn--xs btn-outline--base-two text-nowrap" href="{{ route($routeType . '.review.create', [$order->id, $data->product_id, $data->product_variant_id]) }}">
                                            <i class="la la-star"></i>
                                            <span class="d-none d-md-inline-block">@lang('Review')</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-3 flex-md-row-reverse">
        <div class="col-md-6">
            <div class="details-info-list">
                <h4 class="mb-3">@lang('Order Summary')</h4>
                <ul>
                    <li>
                        <span>@lang('Subtotal')</span>
                        <span class="fw-semibold">{{ showAmount($order->subtotal, 2) }}</span>
                    </li>

                    <li>
                        <span>(<i class="la la-plus"></i>) @lang('Shipping')</span>
                        <span>{{ showAmount($order->shipping_fee, 2) }}</span>
                    </li>

                    <li class="total">
                        <span>@lang('Total')</span>
                        <span>{{ showAmount($order->total_amount) }}</span>
                    </li>
                </ul>
            </div>

            @if (isset($order->deposit) && $order->deposit->status != 0)
                <div class="details-info-list mt-3">
                    <h4 class="mb-3">@lang('Payment Details')</h4>
                    <ul>
                        <li>
                            <span>@lang('Payment Method')</span>
                            <span>
                                @if ($order->deposit->method_code == 0)
                                    @lang('Cash On Delivery')
                                @else
                                    {{ __($order->deposit->gateway->name) }}
                                @endif
                            </span>
                        </li>

                        <li>
                            <span>@lang('Total Bill')</span>
                            <span>{{ showAmount($order->total_amount) }}</span>
                        </li>

                        @if ($order->deposit->charge > 0)
                            <li>
                                <span>@lang('Gateway Charge')</span>
                                <span>{{ gs('cur_sym') . getAmount($order->deposit->charge) }}</span>
                            </li>
                        @endif

                        <li class="total">
                            <span>@lang('Total Payable Amount') </span>
                            <span>{{ gs('cur_sym') . getAmount($order->deposit->amount + $order->deposit->charge) }}</span>
                        </li>

                    </ul>
                </div>
            @endif
        </div>

        <div class="col-md-6">
            @if ($order?->shipping_address)
                <div class="details-info-address border-bottom">
                    <h4 class="mb-3">@lang('Shipping Details')</h4>
                    <ul class="info-address-list">
                        <li>
                            <strong class="title">@lang('Name') </strong>
                            <span>
                                <span class="divide-colon">:</span>
                                {{ $order?->shipping_address?->firstname . ' ' . $order?->shipping_address?->lastname }}
                            </span>
                        </li>
                        <li>
                            <strong class="title">@lang('Address')</strong>
                            <span>
                                <span class="divide-colon">:</span>
                                {{ $order?->shipping_address?->address }}
                            </span>
                        </li>
                        <li>
                            <strong class="title">@lang('State')</strong>
                            <span>
                                <span class="divide-colon">:</span>
                                {{ $order?->shipping_address?->state }}
                            </span>
                        </li>
                        <li>
                            <strong class="title">@lang('City')</strong>
                            <span>
                                <span class="divide-colon">:</span>
                                {{ $order?->shipping_address?->city }}
                            </span>
                        </li>
                        <li>
                            <strong class="title">@lang('Zip')</strong>
                            <span>
                                <span class="divide-colon">:</span>
                                {{ $order?->shipping_address?->zip }}
                            </span>
                        </li>
                        <li>
                            <strong class="title">@lang('Country')</strong>
                            <span>
                                <span class="divide-colon">:</span>
                                {{ $order?->shipping_address?->country }}
                            </span>
                        </li>
                        <li>
                            <strong class="title">@lang('Mobile')</strong>
                            <span>
                                <span class="divide-colon">:</span>
                                {{ __($order?->shipping_address?->mobile_code) }}{{ __($order?->shipping_address?->mobile) }}
                            </span>
                        </li>
                    </ul>
                </div>
            @endif

            @if ($routeType == 'user')
                <div class="details-info-address border-bottom mt-3">
                    <h4 class="mb-3">@lang('Vendor Details')</h4>
                    <ul class="info-address-list">
                        @if ($order->vendor_id)
                            <li>
                                <strong class="title">@lang('Name') </strong>
                                <span>
                                    <span class="divide-colon">:</span>
                                    {{ __($order->vendor->company_name) }}</span>
                            </li>
                        @endif

                        <li>
                            <strong class="title">@lang('Email')</strong>
                            <span>
                                <span class="divide-colon">:</span>
                                <a href="mailto:{{ $order->vendor->email }}" class="text--primary">{{ $order->vendor->email }}</a>
                            </span>
                        </li>

                        <li>
                            <strong class="title">@lang('Country')</strong>
                            <span>
                                <span class="divide-colon">:</span>
                                {{ $order->vendor->country_name }}
                            </span>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
