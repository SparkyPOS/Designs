@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="filter-links">
        <a href="{{ route('vendor.order.index') }}"
            class="btn btn--sm {{ request()->routeIs('vendor.order.index') ? 'btn--base' : 'btn-outline--base' }}">@lang('All Orders')</a>
        <a href="{{ route('vendor.order.pending') }}"
            class="btn btn--sm {{ request()->routeIs('vendor.order.pending') ? 'btn--base' : 'btn-outline--base' }}">@lang('Pending')</a>
        <a href="{{ route('vendor.order.processing') }}"
            class="btn btn--sm {{ request()->routeIs('vendor.order.processing') ? 'btn--base' : 'btn-outline--base' }}">@lang('Processing')</a>
        <a href="{{ route('vendor.order.dispatched') }}"
            class="btn btn--sm {{ request()->routeIs('vendor.order.dispatched') ? 'btn--base' : 'btn-outline--base' }}">@lang('Dispatched')</a>
        <a href="{{ route('vendor.order.completed') }}"
            class="btn btn--sm {{ request()->routeIs('vendor.order.completed') ? 'btn--base' : 'btn-outline--base' }}">@lang('Completed')</a>
        <a href="{{ route('vendor.order.canceled') }}"
            class="btn btn--sm {{ request()->routeIs('vendor.order.canceled') ? 'btn--base' : 'btn-outline--base' }}">@lang('Cancelled')</a>
    </div>

    @include('Template::vendor.orders.orders_table')

    @if ($orders->hasPages())
        {{ paginateLinks($orders) }}
    @endif

    <div id="deliverModal" class="modal fade custom--modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">@lang('Deliver Product')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--base btn--sm w-100 ">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <form>
        <div class="input-group input--group">
            <input type="search" name="search" class="form-control form--control" value="{{ request()->search }}"
                placeholder="@lang('Order Number')">
            <button class="input-group-text btn btn--base text-white">
                <i class="las la-search"></i>
            </button>
        </div>
    </form>
@endpush
@push('style')
    <style>
        .filter-links {
            max-width: 100%;
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            scroll-behavior: smooth;
            scrollbar-width: thin;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }

        .filter-links>.btn {
            min-width: fit-content;
        }

        .nav--link {
            padding: .5rem 1rem;
            border-radius: 0.325rem;
            color: #545454;
            border: 1px solid hsl(var(--border));
            flex-shrink: 0;
        }

        .nav--link.active {
            background-color: hsl(var(--base));
            border-color: hsl(var(--base));
            color: hsl(var(--black));
        }

        @media(max-width: 575px) {
            .nav--link {
                flex-shrink: 1;
                flex-grow: 1;
                flex-basis: calc(50% - .5rem);
            }
        }

        @media(max-width: 480px) {
            .nav--link {
                padding: .25rem 1rem;
            }
        }
    </style>
@endpush
