@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="notice"></div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            @php
                $kyc = getContent('kyc.content', true);
            @endphp
            @if (auth('vendor')->user()->kv == Status::KYC_UNVERIFIED && auth('vendor')->user()->kyc_rejection_reason)
                <div class="alert alert-danger mb-5" role="alert">
                    <div class="d-flex justify-content-between">
                        <h4 class="alert-heading mb-0">@lang('KYC Documents Rejected')</h4>
                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#kycRejectionReason">
                            @lang('Show Reason')</a>
                    </div>
                    <hr>
                    <p class="mb-0">{{ __($kyc?->data_values?->reject) }} <a
                            href="{{ route('vendor.kyc.form') }}">@lang('Click Here to Re-submit Documents')</a>.</p>
                    <br>
                    <a href="{{ route('vendor.kyc.data') }}">@lang('See KYC Data')</a>
                </div>
            @elseif(auth('vendor')->user()->kv == Status::KYC_UNVERIFIED)
                <div class="alert alert-info mb-5" role="alert">
                    <h4 class="alert-heading">@lang('KYC Verification Required')</h4>
                    <hr>
                    <p class="mb-0">{{ __($kyc?->data_values?->required) }} <a
                            href="{{ route('vendor.kyc.form') }}">@lang('Click Here to Submit Documents')</a></p>
                </div>
            @elseif(auth('vendor')->user()->kv == Status::KYC_PENDING)
                <div class="alert alert-warning mb-5" role="alert">
                    <h4 class="alert-heading">@lang('KYC Verification Pending')</h4>
                    <hr>
                    <p class="mb-0">{{ __($kyc?->data_values?->pending) }} <a
                            href="{{ route('vendor.kyc.data') }}">@lang('See KYC Data')</a></p>
                </div>
            @endif
        </div>
    </div>

    <section class="card-list">
        <div class="row gx-lg-2 gx-xl-3 gx-xl-4 g g-3">
            <div class="col-lg-6 col-xxl-4 col-sm-6">
                <div class="user-dashboard-card base">
                    <a href="{{ route('vendor.withdraw.history') }}" class="position-absolute inset-0"></a>
                    <p class="user-dashboard-card__title">
                        <span class="user-dashboard-card__title-icon">
                            <i class="fa-solid fa-money-check-dollar"></i>
                        </span>
                        @lang('Balance')
                    </p>
                    <h2 class="user-dashboard-card__amount">{{ showAmount($vendor->balance) }}</h2>
                    <span class="user-dashboard-card__overlay">
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </div>
            </div>
            <div class="col-lg-6 col-xxl-4 col-sm-6">
                <div class="user-dashboard-card info">
                    <a href="{{ route('vendor.order.index') }}" class="position-absolute inset-0"></a>
                    <p class="user-dashboard-card__title">
                        <span class="user-dashboard-card__title-icon">
                            <i class="fa-solid fa-cart-flatbed"></i>
                        </span>
                        @lang('Total Sold')
                    </p>
                    <h2 class="user-dashboard-card__amount">{{ showAmount($widget['totalOrderAmount']) }}</h2>
                    <span class="user-dashboard-card__overlay">
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </div>
            </div>
            <div class="col-lg-6 col-xxl-4 col-sm-6">
                <div class="user-dashboard-card">
                    <a href="{{ route('vendor.withdraw.history') }}" class="position-absolute inset-0"></a>
                    <p class="user-dashboard-card__title">
                        <span class="user-dashboard-card__title-icon">
                            <i class="fa-solid fa-wallet"></i>
                        </span>
                        @lang('Total Withdrawls')
                    </p>
                    <h2 class="user-dashboard-card__amount">{{ showAmount($widget['totalWithdrawals']) }}</h2>
                    <span class="user-dashboard-card__overlay">
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </div>
            </div>
            <div class="col-lg-6 col-xxl-4 col-sm-6">
                <div class="user-dashboard-card primary">
                    <a href="{{ route('vendor.order.index') }}" class="position-absolute inset-0"></a>
                    <p class="user-dashboard-card__title">
                        <span class="user-dashboard-card__title-icon">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </span>
                        @lang('Total Orders')
                    </p>
                    <h2 class="user-dashboard-card__amount">{{ $widget['totalOrders'] }}</h2>
                    <span class="user-dashboard-card__overlay">
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </div>
            </div>
            <div class="col-lg-6 col-xxl-4 col-sm-6">
                <div class="user-dashboard-card warning">
                    <a href="{{ route('vendor.order.pending') }}" class="position-absolute inset-0"></a>
                    <p class="user-dashboard-card__title">
                        <span class="user-dashboard-card__title-icon">
                            <i class="fa fa-pause-circle"></i>
                        </span>
                        @lang('Pending Orders')
                    </p>
                    <h2 class="user-dashboard-card__amount">{{ $widget['pendingOrders'] }}</h2>
                    <span class="user-dashboard-card__overlay">
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </div>
            </div>
            <div class="col-lg-6 col-xxl-4 col-sm-6">
                <div class="user-dashboard-card secondary">
                    <a href="{{ route('vendor.order.processing') }}" class="position-absolute inset-0"></a>
                    <p class="user-dashboard-card__title">
                        <span class="user-dashboard-card__title-icon">
                            <i class="fa fa-play"></i>
                        </span>
                        @lang('On Processing Orders')
                    </p>
                    <h2 class="user-dashboard-card__amount">{{ $widget['processingOrders'] }}</h2>
                    <span class="user-dashboard-card__overlay">
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </div>
            </div>
            <div class="col-lg-6 col-xxl-4 col-sm-6">
                <div class="user-dashboard-card info">
                    <a href="{{ route('vendor.order.dispatched') }}" class="position-absolute inset-0"></a>
                    <p class="user-dashboard-card__title">
                        <span class="user-dashboard-card__title-icon">
                            <i class="fa fa-truck"></i>
                        </span>
                        @lang('Dispatched Orders')
                    </p>
                    <h2 class="user-dashboard-card__amount">{{ $widget['dispatchedOrders'] }}</h2>
                    <span class="user-dashboard-card__overlay">
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </div>
            </div>
            <div class="col-lg-6 col-xxl-4 col-sm-6">
                <div class="user-dashboard-card success">
                    <a href="{{ route('vendor.order.completed') }}" class="position-absolute inset-0"></a>
                    <p class="user-dashboard-card__title">
                        <span class="user-dashboard-card__title-icon">
                            <i class="fa fa-check-circle"></i>
                        </span>
                        @lang('Delivered Orders')
                    </p>
                    <h2 class="user-dashboard-card__amount">{{ $widget['deliveredOrders'] }}</h2>
                    <span class="user-dashboard-card__overlay">
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </div>
            </div>
            <div class="col-lg-6 col-xxl-4 col-sm-6">
                <div class="user-dashboard-card danger">
                    <a href="{{ route('vendor.order.canceled') }}" class="position-absolute inset-0"></a>
                    <p class="user-dashboard-card__title">
                        <span class="user-dashboard-card__title-icon">
                            <i class="fa fa-times-circle"></i>
                        </span>
                        @lang('Canceled Orders')
                    </p>
                    <h2 class="user-dashboard-card__amount">{{ $widget['canceledOrders'] }}</h2>
                    <span class="user-dashboard-card__overlay">
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </div>
            </div>
        </div>
    </section>

    @if (!blank($topSellProducts))
        <div class="product-list">
            <div class="product-list__heading">
                <h2 class="dashboard-heading">@lang('Top Selling Products')</h2>
                <a href="{{ route('vendor.products.index') }}" class="view-all-btn">@lang('View all') <span
                        class="icon"><i class="fas fa-arrow-right"></i></span></a>
            </div>
            <div class="row gx-lg-2 gx-xl-3 gx-xl-4 g g-3">
                @foreach ($topSellProducts as $product)
                    <div class="col-xxl-2 col-xl-3 col-sm-4 col-xsm-6">
                        <div class="product-list__item">
                            <div class="product-list__item-thumb">
                                <img src="{{ $product->mainImage(true) }}" alt="@lang('image')">
                            </div>
                            <div class="product-list__item-content">
                                <p class="product-list__item-title">{{ __($product->name) }}</p>
                                <h4 class="product-list__item-price">{{ $product->formattedPrice() }}</h4>
                                <p class="product-list__item-order">@lang('Total Orders'): <span
                                        class="order-count">{{ getAmount($product->order_details_sum_quantity) }}</span>
                                </p>
                                <p class="product-list__item-order">@lang('Sold'): <span
                                        class="order-count">{{ showAmount($product->order_details_sum_price) }} </span>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="sales-chart">
        <h2 class="dashboard-heading">
            @lang('Sales')
        </h2>
        <div class="sales-chart__wrapper">
            <div class="sales-chart__header flex-between">
                <div></div>
                <div class="sales-chart__select">
                    <div id="salesDatePicker" class="border p-1 cursor-pointer rounded">
                        <i class="la la-calendar"></i>&nbsp;
                        <span></span> <i class="la la-caret-down"></i>
                    </div>
                </div>
            </div>
            <div id="salesReportChart"></div>
        </div>
    </div>

    @if (auth('vendor')->user()->kv == Status::KYC_UNVERIFIED && auth('vendor')->user()->kyc_rejection_reason)
        <div class="modal fade" id="kycRejectionReason">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">@lang('KYC Document Rejection Reason')</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ auth('vendor')->user()->kyc_rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/charts.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush
@push('style')
    <style>
        .alert a {
            text-decoration: underline;
        }
    </style>
@endpush
@push('script')
    <script>
        (function($) {
            'use strict';

            const start = moment().subtract(30, 'days');
            const end = moment();

            const dateRangeOptions = {
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                        .endOf(
                            'month')
                    ],
                    'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                },
                maxDate: moment()
            }

            const changeDatePickerText = (element, startDate, endDate) => {
                $(element).html(startDate.format('MMMM D, YYYY') + ' - ' + endDate.format('MMMM D, YYYY'));
            }

            let salesBarChart = barChart(
                document.querySelector("#salesReportChart"),
                `{{ __(gs('cur_sym')) }}`,
                [{
                    name: 'Ordered',
                    data: []
                }],
                [],
            );

            const salesChart = (startDate, endDate) => {
                const data = {
                    start_date: startDate.format('YYYY-MM-DD'),
                    end_date: endDate.format('YYYY-MM-DD')
                }

                const url = `{{ route('vendor.chart.sales') }}`;
                $.get(url, data,
                    function(data, status) {
                        if (status == 'success') {
                            salesBarChart.updateSeries(data.data);
                            salesBarChart.updateOptions({
                                xaxis: {
                                    categories: data.created_on,
                                }
                            });
                        }
                    }
                );
            }

            $('#salesDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText(
                '#salesDatePicker span', start, end));

            changeDatePickerText('#salesDatePicker span', start, end);
            salesChart(start, end);

            $('#salesDatePicker').on('apply.daterangepicker', (event, picker) => salesChart(picker.startDate, picker
                .endDate));

        })(jQuery);
    </script>
@endpush
