@extends($activeTemplate . 'layouts.master')
@section('content')
    <section class="settings-container">
        <div class="row">
            <div class="col-md-6">
                <h4>@lang('Review of')
                    {{ __($orderProduct?->product->name . ($orderProduct->productVariant ? '(' . $orderProduct->productVariant->name . ')' : '')) }}
                </h4>
                <div class="setting-content">
                    <div class="setting-content__details">
                        <form action="{{ route('user.review.store', $review?->id) }}" method="post">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <input type="hidden" name="product_id" value="{{ $orderProduct->product_id }}">
                            <input type="hidden" name="product_variant_id" value="{{ $orderProduct->product_variant_id }}">
                            <div class="form-group">
                                <label class="form--label">@lang('Title')</label>
                                <input type="text" name="title" value="{{ old('title', $review?->title) }}"
                                    class="form--control" required>
                            </div>
                            <div class="form-group">
                                <label class="form--label">@lang('Review')</label>
                                <textarea type="text" name="review" class="form--control" required>{{ old('review', $review?->review) }}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="from--label">@lang('Rating')</label>
                                <ul class="rating-list rating-three" id="ratingArea">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <li class="rating-list__item disabled userRatingTrigger"
                                            data-rating="{{ $i }}">
                                            <span class="icon"><i class="fa-regular fa-star fa-xl"></i></i></span>
                                        </li>
                                    @endfor
                                </ul>
                                <input type="hidden" name="rating" id="rating"
                                    value="{{ old('rating', $review?->rating) }}" required>
                            </div>
                            <div>
                                <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('breadcrumb-plugins')
    @if (request()->routeIs('user.review.create'))
        <a href="{{ route('user.order.details', $order->order_number) }}" class="btn btn-outline--base-two text-white">
            <i class="las la-angle-left"></i>
            @lang('Back to Order Details')
        </a>
    @else
        <a href="{{ route('user.review.index') }}" class="btn btn-outline--base-two text-white">
            <i class="las la-angle-left"></i>
            @lang('Back to Reviews')
        </a>
    @endif
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.userRatingTrigger').on('click', function() {
                var rating = $(this).data('rating');
                ratingBg(rating);
            });

            const ratingBg = (rating) => {
                $('#rating').val(rating);
                $('.userRatingTrigger').addClass('disabled');
                $('.userRatingTrigger').find('i').removeClass('fa-solid');
                $('#ratingArea').removeClass('success-deep success warning danger');

                for (var i = 1; i <= rating; i++) {
                    $(`.userRatingTrigger[data-rating="${i}"]`).removeClass('disabled');
                    $(`.userRatingTrigger[data-rating="${i}"]`).find('i').addClass('fa-solid');
                }
                $('#ratingArea').addClass(getRatingColorClass(rating));
            }

            function getRatingColorClass(rating) {
                if (rating == 5) {
                    return 'success-deep';
                } else if (rating >= 4) {
                    return 'success';
                } else if (rating >= 3) {
                    return 'warning';
                } else if (rating > 0) {
                    return 'danger';
                } else {
                    return '';
                }
            }

            ratingBg(`{{ old('rating', $review?->rating) }}`);

        })(jQuery);
    </script>
@endpush
