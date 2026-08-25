@extends('Template::layouts.app')
@section('main')
    <div class="dashboard">

        @if (request()->routeIs('vendor.*'))
            @include('Template::partials.vendor_sidenav')
        @else
            @include('Template::partials.user_sidenav')
        @endif

        <div class="dashboard__body">
            @if (request()->routeIs('vendor.*'))
                @include('Template::partials.vendor_header')
            @else
                @include('Template::partials.user_header')
            @endif

            <div class="container-fluid">
                <div class="dashboard__header flex-between gap-2">
                    <h3 class="dashboard__header-title flex-fill">{{ __($pageTitle) }}</h3>

                    <div class="d-flex flex-wrap gap-2">
                        @stack('breadcrumb-plugins')
                    </div>
                </div>
                <div class="dashboard__content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endsection
