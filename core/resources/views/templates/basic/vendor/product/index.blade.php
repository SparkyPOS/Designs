@extends($activeTemplate . 'layouts.master')
@section('content')
    <form class="order-management__header">
        <div class="db__search">
            <button type="submit" class="ps-2" for="search">
                <i class="la la-search"></i>
            </button>
            <input type="search" name="search" value="{{ request()->search }}" id="search" placeholder="@lang('Search product')">
        </div>
    </form>
    <div class="order-management__body">
        <div class="order-management__table">
            <div class="order-management__table-header">
                <span class="table-header product-item">@lang('Product')</span>
                <span class="table-header">@lang('Catalog')</span>
                <span class="table-header">@lang('Total Order')</span>
                <span class="table-header">@lang('Completed Order')</span>
                <span class="table-header">@lang('Pending Order')</span>
                <span class="table-header">@lang('Status')</span>
                <span class="table-header product-item__actions">@lang('Action')</span>
            </div>

            <div class="order-management__table-body">
                @forelse($products as $product)
                    <div class="order-management__table-body-item">

                        <div class="product-item table-body-item">
                            <span class="product-item__thumbnail image-popup" data-image="{{ $product->mainImage(true) }}">
                                <img src="{{ $product->mainImage(true) }}" alt="cover-thumb">
                            </span>
                            <div class="product-item__info">
                                <h3 class="product-item__title">{{ strLimit($product->name, 40) }}</h3>
                                @php echo $product->formattedPrice(); @endphp
                            </div>
                        </div>
                        <p class="table-body-item">
                            {{ __($product?->catalog?->catalogCategory?->name) }}
                            <span>
                                <i class="las la-angle-right"></i>
                            </span>
                            {{ __($product?->catalog?->name) }}
                        </p>
                        <p class="table-body-item">
                            <strong class="d-xl-none pe-2">@lang('Total Order'): </strong>
                            {{ showAmount($product->orderDetails->count(), 0, currencyFormat: false) }}
                        </p>
                        <p class="table-body-item">
                            <strong class="d-xl-none pe-2">@lang('Completed Order'): </strong>
                            {{ showAmount($product->totalCompletedOrder, 0, currencyFormat: false) }}
                        </p>
                        <p class="table-body-item">
                            <strong class="d-xl-none pe-2">@lang('Pending Order'): </strong>
                            {{ showAmount($product->totalPendingOrder, 0, currencyFormat: false) }}
                        </p>
                        <p class="table-body-item status-badge">@php echo $product->publishBadge; @endphp</p>

                        <div class="table-body-item product-item__actions">
                            @if ($product->is_published == Status::ENABLE)
                                <button type="button" class="edit-btn confirmationBtn" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="@lang('Unpublish')"
                                    data-action="{{ route('vendor.products.status', $product->id) }}"
                                    data-question="@lang('Are you sure to unpublish this product?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                        height="24" color="hsl(var(--danger))" fill="none">
                                        <path
                                            d="M19.439 15.439C20.3636 14.5212 21.0775 13.6091 21.544 12.955C21.848 12.5287 22 12.3155 22 12C22 11.6845 21.848 11.4713 21.544 11.045C20.1779 9.12944 16.6892 5 12 5C11.0922 5 10.2294 5.15476 9.41827 5.41827M6.74742 6.74742C4.73118 8.1072 3.24215 9.94266 2.45604 11.045C2.15201 11.4713 2 11.6845 2 12C2 12.3155 2.15201 12.5287 2.45604 12.955C3.8221 14.8706 7.31078 19 12 19C13.9908 19 15.7651 18.2557 17.2526 17.2526"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                        <path
                                            d="M9.85786 10C9.32783 10.53 9 11.2623 9 12.0711C9 13.6887 10.3113 15 11.9289 15C12.7377 15 13.47 14.6722 14 14.1421"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                        <path d="M3 3L21 21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                            @else
                                <button type="button" class="edit-btn confirmationBtn" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="@lang('Published')"
                                    data-action="{{ route('vendor.products.status', $product->id) }}"
                                    data-question="@lang('Are you sure to published this product?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                        height="24" color="hsl(var(--success))" fill="none">
                                        <path
                                            d="M21.544 11.045C21.848 11.4713 22 11.6845 22 12C22 12.3155 21.848 12.5287 21.544 12.955C20.1779 14.8706 16.6892 19 12 19C7.31078 19 3.8221 14.8706 2.45604 12.955C2.15201 12.5287 2 12.3155 2 12C2 11.6845 2.15201 11.4713 2.45604 11.045C3.8221 9.12944 7.31078 5 12 5C16.6892 5 20.1779 9.12944 21.544 11.045Z"
                                            stroke="currentColor" stroke-width="1.5"></path>
                                        <path
                                            d="M15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15C13.6569 15 15 13.6569 15 12Z"
                                            stroke="currentColor" stroke-width="1.5"></path>
                                    </svg>
                                </button>
                            @endif
                            <a href="{{ route('vendor.products.edit', $product->slug) }}" class="edit-btn"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Edit')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
                                    fill="none">
                                    <path
                                        d="M16.4249 4.60509L17.4149 3.6151C18.2351 2.79497 19.5648 2.79497 20.3849 3.6151C21.205 4.43524 21.205 5.76493 20.3849 6.58507L19.3949 7.57506M16.4249 4.60509L9.76558 11.2644C9.25807 11.772 8.89804 12.4078 8.72397 13.1041L8 16L10.8959 15.276C11.5922 15.102 12.228 14.7419 12.7356 14.2344L19.3949 7.57506M16.4249 4.60509L19.3949 7.57506"
                                        stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                    <path
                                        d="M18.9999 13.5C18.9999 16.7875 18.9999 18.4312 18.092 19.5376C17.9258 19.7401 17.7401 19.9258 17.5375 20.092C16.4312 21 14.7874 21 11.4999 21H11C7.22876 21 5.34316 21 4.17159 19.8284C3.00003 18.6569 3 16.7712 3 13V12.5C3 9.21252 3 7.56879 3.90794 6.46244C4.07417 6.2599 4.2599 6.07417 4.46244 5.90794C5.56879 5 7.21252 5 10.5 5"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                            <a href="{{ route('product.details', $product->slug) }}" class="edit-btn"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Quick Preview')"
                                target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-view-icon lucide-view">
                                    <path d="M21 17v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2" />
                                    <path d="M21 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2" />
                                    <circle cx="12" cy="12" r="1" />
                                    <path
                                        d="M18.944 12.33a1 1 0 0 0 0-.66 7.5 7.5 0 0 0-13.888 0 1 1 0 0 0 0 .66 7.5 7.5 0 0 0 13.888 0" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-muted text-center">@lang('No product yet')</div>
                @endforelse

            </div>

        </div>
    </div>
    @if ($products->hasPages())
        {{ paginateLinks($products) }}
    @endif

    <x-confirmation-modal />

    @include($activeTemplate . 'partials.image_popup_modal')
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('vendor.products.create') }}" class="btn btn-outline--base-two text-white">
        <i class="las la-plus"></i>
        @lang('Add Product')
    </a>
@endpush
