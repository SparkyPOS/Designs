@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="mb-2 pb-2 d-flex gap-xl-3 gap-md-2 gap-1 overflow-auto ">
                <a href="{{ route('admin.order.index') }}"
                    class="btn btn-outline-primary flex-shrink-0  {{ request()->routeIs('admin.order.index') ? 'active' : '' }}">@lang('All Orders')</a>
                <a href="{{ route('admin.order.pending') }}"
                    class="btn btn-outline-primary flex-shrink-0  {{ request()->routeIs('admin.order.pending') ? 'active' : '' }}">@lang('Pending')</a>
                <a href="{{ route('admin.order.processing') }}"
                    class="btn btn-outline-primary flex-shrink-0  {{ request()->routeIs('admin.order.processing') ? 'active' : '' }}">@lang('Processing')</a>
                <a href="{{ route('admin.order.dispatched') }}"
                    class="btn btn-outline-primary flex-shrink-0  {{ request()->routeIs('admin.order.dispatched') ? 'active' : '' }}">@lang('Dispatched')</a>
                <a href="{{ route('admin.order.delivered') }}"
                    class="btn btn-outline-primary flex-shrink-0  {{ request()->routeIs('admin.order.delivered') ? 'active' : '' }}">@lang('Completed')</a>
                <a href="{{ route('admin.order.canceled') }}"
                    class="btn btn-outline-primary flex-shrink-0  {{ request()->routeIs('admin.order.canceled') ? 'active' : '' }}">@lang('Cancelled')</a>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Order Date')</th>
                                    <th>@lang('Order ID')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('Vendor')</th>
                                    <th>@lang('Commission')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Payment Status')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse($orders as $order)
                                    <tr>
                                        <td>{{ showDateTime($order->created_at, 'd M, Y') }}</td>
                                        <td>{{ $order->order_number }}</td>
                                        <td>
                                            <a
                                                href="{{ route('admin.users.detail', $order->user->id) }}"><span>@</span>{{ $order->user->username }}</a>
                                        </td>
                                        <td>
                                            <a
                                                href="{{ route('admin.vendors.detail', $order->vendor->id) }}"><span>@</span>{{ $order->vendor->username }}</a>
                                        </td>
                                        <td>
                                            <strong>{{ showAmount($order->commission_amount) }}</strong>
                                        </td>
                                        <td>
                                            <strong>{{ showAmount($order->total_amount) }}</strong>
                                        </td>

                                        <td>@php echo $order->paymentBadge() @endphp</td>
                                        <td>@php echo $order->statusBadge() @endphp</td>
                                        <td>
                                            <div class="btn--group">
                                                <a href="{{ route('admin.order.details', $order->id) }}"
                                                    class="btn btn-outline--dark btn-sm">
                                                    <i class="la la-desktop"></i>@lang('Details')
                                                </a>
                                                @if ($order->status == Status::ORDER_CANCELED)
                                                    <button type="submit" class="btn btn-outline--primary btn-sm confirmationBtn"
                                                     data-action="{{ route('admin.order.payment.returned', $order->id) }}"
                                                    data-question="@lang('Are you sure you want to mark this order as payment returned?')">
                                                        <i class="las la-exchange-alt"></i>@lang('Returned')
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-outline--dark btn-sm" disabled>
                                                        <i class="las la-exchange-alt"></i>@lang('Returned')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($orders->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($orders) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form />
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';
            $('.deliverDPBtn').on('click', function() {
                let data = $(this).data();

                let modal = $('#deliverModal');
                let html = `<h6 class="question">${data.question}</h6>`;

                if (data.after_sale_downloadable_products.length) {
                    html += `<div class="alert alert-info p-3 my-2">
                            <small class="text--info">@lang('This order has an after sale downloadable product. So, if you want to mark the order as delivered you need to submit the file here.')</small>
                        </div>`;

                    $.each(data.after_sale_downloadable_products, function(index, product) {
                        html += `<div class="form-group">
                                <label class="required">${nameToTitle(product.name)}</label>
                                <input type="file" name="download_file[${product.id}]" accept=".zip" class="form-control" required>
                            </div>`
                    });
                } else if (data.has_physical_product == 0) {
                    html += `<small class="text--info">@lang('Note: This order contains only instant downloadable products. Once processing is complete, the order status will automatically be updated to "Delivered."')</small>`;
                }

                modal.find('form').attr('action', data.action);
                modal.find('.modal-body').html(html);
                modal.modal('show');
            })

            function nameToTitle(name) {
                return name.toLowerCase().split(' ').map(function(word) {
                    return word.charAt(0).toUpperCase() + word.slice(1);
                }).join(' ');
            }
        })(jQuery);
    </script>
@endpush
