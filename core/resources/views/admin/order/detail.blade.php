@extends('admin.layouts.app')

@section('panel')
    <div class="card mb-3">
        <div class="card-body p-0">
            <div class="order-details-products">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Product')</th>
                                <th>@lang('Price')</th>
                                <th>@lang('Quantity')</th>
                                <th>@lang('Total Price')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $subtotal = $order->orderDetail->sum(function ($detail) {
                                    return $detail->price * $detail->quantity;
                                });
                            @endphp

                            @foreach ($order->orderDetail as $data)
                                @php
                                    $mainImage = $data->productVariant && $data->productVariant->main_image_id ? $data->productVariant->mainImage(true) : $data->product->mainImage(true);
                                @endphp

                                <tr>
                                    <td>
                                        <div class="single-product-item">
                                            <div class="thumb">
                                                <img class="lazyload" src="{{ $mainImage }}" alt="product-image">
                                            </div>

                                            <div class="content">
                                                <span class="title fw-semibold d-block text-wrap">
                                                    {{ strLimit($data->product->name, 60) }}

                                                    @if ($data->productVariant)
                                                        - {{ $data->productVariant->name }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        {{ showAmount($data->price) }}
                                    </td>
                                    <td>{{ $data->quantity }}</td>
                                    <td class="text-end">{{ showAmount($data->price * $data->quantity) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="d-flex flex-column gap-3">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">@lang('Customer Details')</h6>
                    </div>
                    <div class="card-body">
                        <ul class="info-address-list">
                            @if ($order->user_id)
                                <li>
                                    <span class="title">@lang('Name') </span>
                                    <span class="value">
                                        <a href="{{ route('admin.users.detail', $order->user->id) }}">{{ $order->user->fullname }}</a>
                                    </span>
                                </li>
                            @endif

                            <li>
                                <span class="title">@lang('Email')</span>
                                <span class="value">
                                    {{ $order->user->email }}
                                </span>
                            </li>

                            <li>
                                <span class="title">@lang('Mobile')</span>
                                <span class="value">
                                    {{ $order->user->mobile_number }}
                                </span>
                            </li>

                            <li>
                                <span class="title">@lang('Country')</span>
                                <span class="value">
                                    {{ $order->user->country_name }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">@lang('Vendor Details')</h6>
                    </div>
                    <div class="card-body">
                        <ul class="info-address-list">
                            @if ($order->vendor_id)
                                <li>
                                    <span class="title">@lang('Name') </span>
                                    <span class="value">
                                        <a href="{{ route('admin.vendors.detail', $order->vendor->id) }}">{{ $order->vendor->fullname }}</a>
                                    </span>
                                </li>
                            @endif

                            <li>
                                <span class="title">@lang('Email')</span>
                                <span class="value">
                                    {{ $order->vendor->email }}
                                </span>
                            </li>

                            <li>
                                <span class="title">@lang('Mobile')</span>
                                <span class="value">
                                    {{ $order->vendor->mobile_number }}
                                </span>
                            </li>

                            <li>
                                <span class="title">@lang('Country')</span>
                                <span class="value">
                                    {{ $order->vendor->country_name }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                @if ($order->shipping_address)
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">@lang('Shipping Details')</h6>
                        </div>
                        <div class="card-body">
                            <ul class="info-address-list">
                                <li>
                                    <span class="title">@lang('Name') </span>
                                    <span class="value">
                                        {{ $order?->shipping_address?->firstname . ' ' . $order?->shipping_address?->lastname }}
                                    </span>
                                </li>

                                <li>
                                    <span class="title">@lang('Address')</span>
                                    <span class="value">
                                        {{ $order?->shipping_address?->address }}
                                    </span>
                                </li>

                                <li>
                                    <span class="title">@lang('State')</span>
                                    <span class="value">
                                        {{ $order?->shipping_address?->state }}
                                    </span>
                                </li>

                                <li>
                                    <span class="title">@lang('City')</span>
                                    <span class="value">
                                        {{ $order?->shipping_address?->city }}
                                    </span>
                                </li>

                                <li>
                                    <span class="title">@lang('Zip')</span>
                                    <span class="value">
                                        {{ $order?->shipping_address?->zip }}
                                    </span>
                                </li>

                                <li>
                                    <span class="title">@lang('Country')</span>
                                    <span class="value">
                                        {{ $order?->shipping_address?->country }}
                                    </span>
                                </li>
                                <li>
                                    <span class="title">@lang('Mobile')</span>
                                    <span class="value">
                                        {{ __($order?->shipping_address?->mobile_code) }}{{ __($order?->shipping_address?->mobile) }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex flex-column gap-3">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">@lang('Order Details')</h6>
                    </div>
                    <div class="card-body">
                        <ul class="info-address-list">
                            <li>
                                <span class="title">@lang('Date') </span>
                                <span class="value">
                                    {{ showDateTime($order->created_at, 'F d, Y') }}
                                    @lang('at')
                                    {{ showDateTime($order->created_at, 'h:i A') }}
                                </span>
                            </li>
                            <li>
                                <span class="title">@lang('Payment Status')</span>
                                <span class="value">
                                    @php echo $order->paymentBadge() @endphp
                                </span>
                            </li>
                            <li>
                                <span class="title">@lang('Order Status')</span>
                                <span class="value">
                                    @php echo $order->statusBadge() @endphp
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">@lang('Order Summary')</h6>
                    </div>
                    <div class="card-body">
                        <ul class="details-info-list">
                            <li>
                                <span>@lang('Subtotal')</span>
                                <span class="fw-semibold">{{ showAmount($subtotal, 2) }}</span>
                            </li>

                            <li>
                                <span>(<i class="la la-plus"></i>) @lang('Shipping')</span>
                                <span>{{ showAmount($order->shipping_fee) }}</span>
                            </li>

                            <li class="total">
                                <span>@lang('Total')</span>
                                <span>{{ showAmount($order->total_amount) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                @if (isset($order->deposit) && $order->deposit->status != 0)
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">@lang('Payment Details')</h6>
                        </div>
                        <div class="card-body">
                            <ul class="details-info-list">
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

                                @if ($order?->deposit->charge > 0)
                                    <li>
                                        <span>@lang('Gateway Charge')</span>
                                        <span>{{ showAmount($order?->deposit->charge) }}</span>
                                    </li>
                                @endif

                                <li class="total">
                                    <span>@lang('Total Payable Amount') </span>
                                    <span>{{ showAmount($order->deposit->amount + $order?->deposit->charge) }}</span>
                                </li>

                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .single-product-item {
            max-width: 230px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .single-product-item .content {
            flex-grow: 1;
        }

        .single-product-item .thumb {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            background: #fff;
            border-radius: 5px;
            overflow: hidden;
            width: 48px;
            height: 48px;
            flex-shrink: 0;
        }

        .order-details-top {
            margin-bottom: 12px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
        }

        .order-details-product {
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .details-info-list li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .details-info-list li span:first-child {
            font-weight: 500;
            color: #64748b;
        }

        .details-info-list li span:last-child {
            font-weight: 600;
            color: #334155;
        }

        .details-info-list li:not(:last-child) {
            margin-bottom: 12px;
        }

        .details-info-list li.total {
            border-top: 1px solid #ebebeb;
            padding-top: 12px;
            font-size: 1rem;
        }

        .info-address-list li {
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            gap: 24px;
        }

        .info-address-list li:not(:last-child) {
            margin-bottom: 12px;
        }

        .info-address-list li .title {
            width: 100%;
            min-width: 80px;
            max-width: 30%;
            font-weight: 500;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            color: #334155;
        }

        .info-address-list li .title::after {
            content: ':';
        }

        .info-address-list li .value {
            flex-grow: 1;
            font-weight: 500;
            color: #64748b;
        }

        @media (max-width: 767px) {
            .info-address-list li {
                gap: 12px;
            }

            .order-details-products .single-product-item {
                flex-direction: column;
                align-items: flex-end;
                margin-left: auto;
            }

            .order-details-products .content-top {
                margin-bottom: 0;
            }

            .order-details-top {
                flex-direction: column-reverse;
                align-items: flex-start;
            }
        }

        @media (max-width: 1399px) {
            .details-info-list {
                max-width: 100%;
            }
        }
    </style>
@endpush
