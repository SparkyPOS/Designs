@extends($activeTemplate . 'layouts.master')
@section('content')
    <section class="card-list">
        <div class="row gx-lg-2 gx-xl-3 gx-xl-4 g g-3">
            <div class="col-lg-6 col-xxl-4 col-sm-6">
                <div class="user-dashboard-card base">
                    <a href="{{ route('user.order.index') }}" class="position-absolute inset-0"></a>
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
                <div class="user-dashboard-card success">
                    <a href="{{ route('user.order.completed') }}" class="position-absolute inset-0"></a>
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
                <div class="user-dashboard-card warning">
                    <a href="{{ route('user.order.pending') }}" class="position-absolute inset-0"></a>
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
                <div class="user-dashboard-card info">
                    <a href="{{ route('user.order.processing') }}" class="position-absolute inset-0"></a>
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
                <div class="user-dashboard-card success">
                    <a href="{{ route('user.order.dispatched') }}" class="position-absolute inset-0"></a>
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
                <div class="user-dashboard-card danger">
                    <a href="{{ route('user.order.canceled') }}" class="position-absolute inset-0"></a>
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
        <div class="row pt-3 pt-md-4 pt-lg-5">
            <div class="col-md-12">
                <h2 class="dashboard-heading">@lang('Latest Orders')</h2>
                @include('Template::user.orders.orders_table', ['orders' => $latestOrders])
            </div>
        </div>
    </section>
@endsection
