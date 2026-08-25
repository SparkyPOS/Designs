@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="search-section mt-30 mb-3">
        <form class="container">
            <div class="db__search">
                <label for="productSearch">
                    <i class="la la-search la-lg"></i>
                </label>
                <input type="search" name="search" value="{{ request()->search }}" id="productSearch"
                    placeholder="@lang('Search product')">
            </div>
        </form>
    </div>
    <div class="product-breadcrumb">
        <div class="container">
            <div class="product-breadcrumb__wrapper">
                <a href="javascript:void(0)" class="product-breadcrumb__link"> @lang('Product') </a>
                <a href="javascript:void(0)" class="product-breadcrumb__link"> {{ __($vendor?->company_name) }} </a>
            </div>
        </div>
    </div>

    <section class="new-arrivals-section my-60 mt-30">
        <div class="container">
            <div class="catalog-heading__wrapper ">
                <div class="catalog-heading">
                    <h2 class="catalog-heading__title flex-between gap-2 flex-nowrap flex-fill">
                        {{ __($vendor->company_name) }} @lang('Products')</h2>
                    <div class="catalog-heading__btn flex-align gap-2">
                        <div class="short-by flex-align gap-2">
                            <span class="short-by__name">@lang('Sort By')</span>
                            <select name="sort_by" id="selection-active" class="custom-selection">
                                <option value="">@lang('Default')</option>
                                <option value="desc" @selected(request()->sort_by == 'desc')>@lang('Price high to low')</option>
                                <option value="asc" @selected(request()->sort_by == 'asc')>@lang('Price low to high')</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xxl-12">
                    <div class="row g-md-4 gx-5" id="productListArea">
                        @include($activeTemplate . '.partials.product')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            $('select[name="sort_by"]').on('change', function() {
                let searchValue = $('#productSearch').val();
                let requestUrl =
                    `{{ route('product.vendor', $vendor->username) }}?search=${searchValue}&sort_by=${$(this).val()}`
                window.history.pushState({
                    path: requestUrl
                }, '', requestUrl);

                $(".preloader").fadeIn();

                $.ajax({
                    url: requestUrl,
                    type: 'GET',
                    success: function(response) {
                        $('#productListArea').html(response.data.html);
                        $(".preloader").fadeOut();
                    },
                    error: function(xhr) {
                        $(".preloader").fadeOut();
                        notify('error', xhr.statusText);
                    }
                });
            });

        })(jQuery);
    </script>
@endpush
