@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="new-arrivals-section my-60 mt-30">
        <div class="container">
            <div class="catalog-heading__wrapper ">
                <div class="catalog-heading">
                    <h2 class="catalog-heading__title flex-between gap-2 flex-nowrap flex-fill">
                        @lang('Review of') {{ __($product->name) }}</h2>
                    <div class="catalog-heading__btn flex-align gap-2">
                        <div class="short-by flex-align gap-2">
                            <span class="short-by__name">@lang('Sort By')</span>
                            <select name="sort_by" id="selection-active" class="custom-selection">
                                <option value="">@lang('Default')</option>
                                <option value="asc" @selected(request()->sort_by == 'asc')>@lang('New')</option>
                                <option value="desc" @selected(request()->sort_by == 'desc')>@lang('Old')</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div id="reviewAreas">
                @include($activeTemplate . '.partials.reviews')
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
                    `{{ route('product.reviews', $product->slug) }}?sort_by=${$(this).val()}`
                window.history.pushState({
                    path: requestUrl
                }, '', requestUrl);

                $(".preloader").fadeIn();

                $.ajax({
                    url: requestUrl,
                    type: 'GET',
                    success: function(response) {
                        $('#reviewAreas').html(response.data.html);
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
