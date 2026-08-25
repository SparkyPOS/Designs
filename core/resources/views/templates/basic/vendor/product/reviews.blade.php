@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>@lang('Product')</th>
                            <th class="text-center">@lang('Order Number')</th>
                            <th class="text-center">@lang('Customer')</th>
                            <th class="text-center">@lang('Title')</th>
                            <th class="text-center">@lang('Rating')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td>{{ __($review?->product?->name) }}</td>
                                <td>{{ $review?->order?->order_number }}</td>
                                <td>{{ __($review?->user?->fullname) }}</td>
                                <td>{{ __($review->title) }}</td>
                                <td>{{ getAmount($review->rating) }}</td>
                                <td>
                                    <button class="btn btn--xs btn--base detailBtn showReview" data-review="{{ $review }}"
                                        data-name="{{ __($review?->user?->fullname) }}"
                                        data-datetime="{{ showDateTime($review->created_at) }}">
                                        <i class="la la-desktop"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center">@lang('No revies found!')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($reviews->hasPages())
        {{ paginateLinks($reviews) }}
    @endif


    <div class="modal fade custom--modal" id="showReviewModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title " id="exampleModalLabel">
                        @lang('Review By') <span class="name"></span>
                    </h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
                        <div class="rating"></div>
                        <small class="datetime text-muted"></small>
                    </div>
                    <h4 class="title  mb-2"></h4>
                    <p class="review mb-4 fs-18"></p>
                    <div class="text-end">

                        <button type="button" class="btn btn--xs btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <form>
        <div class="input-group input--group">
            <input type="search" name="search" class="form-control form--control" value="{{ request()->search }}"
                placeholder="@lang('Title, Order Number, Product Name')">
            <button class="input-group-text bg--base">
                <i class="las la-search"></i>
            </button>
        </div>
    </form>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            const modal = $('#showReviewModal');
            $('.showReview').on('click', function() {
                let review = $(this).data('review');
                let name = $(this).data('name');
                let datetime = $(this).data('datetime');

                modal.find('.name').text(name);
                modal.find('.datetime').text(datetime);
                modal.find('.title').text(review.title);
                modal.find('.rating').html(showRating(review.rating));
                modal.find('.review').text(review.review);
                modal.modal('show');
            });

            function showRating(rating) {
                let html = `<ul class="rating-list">`;
                for (let i = 1; i <= 5; i++) {
                    let disabled = i <= rating ? "" : "disabled";
                    html += `<li class="rating-list__item ${ disabled }">
                        <span class = "icon" > <i class = "fa-${ disabled?'regular':'solid' } fa-star fa-xl" > </i></span>
                        </li>`;
                }

                html += `</ul>`;
                return html;
            }

        })(jQuery);
    </script>
@endpush
