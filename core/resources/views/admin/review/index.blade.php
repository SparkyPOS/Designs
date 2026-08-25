@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Vendor')</th>
                                    <th class="text-center">@lang('Rating')</th>
                                    <th class="text-center">@lang('Product')</th>
                                    <th class="text-center">@lang('Customer')</th>
                                    <th class="text-center">@lang('Order Number')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $review)
                                    <tr>
                                        <td>{{ __($review->title) }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $review->order?->vendor?->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a
                                                    href="{{ route('admin.vendors.detail', $review->order?->vendor?->id ?? 0) }}"><span>@</span>{{ $review->order?->vendor?->username }}</a>
                                            </span>
                                        </td>
                                        <td>{{ getAmount($review->rating) }}</td>
                                        <td>{{ __($review?->product?->name) }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $review->user?->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a
                                                    href="{{ route('admin.users.detail', $review->user?->id ?? 0) }}"><span>@</span>{{ $review->user?->username }}</a>
                                            </span>
                                        </td>
                                        <td>{{ $review?->order?->order_number }}</td>
                                        <td>
                                            <button class="btn btn-outline-primary btn-sm showReview"
                                                data-review="{{ $review }}"
                                                data-name="{{ __($review?->user?->fullname) }}"
                                                data-datetime="{{ showDateTime($review->created_at) }}">
                                                <i class="la la-eye"></i>
                                                @lang('View')
                                            </button>
                                            <button class="btn btn-outline--danger btn--sm confirmationBtn"
                                                data-action="{{ route('admin.reviews.delete', $review->id) }}"
                                                data-question="@lang('Are you sure to delete this review?')">
                                                <i class="la la-trash"></i>
                                                @lang('Delete')
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">@lang('No reviews found!')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($reviews->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($reviews) }}
                    </div>
                @endif
            </div>
        </div>
    </div>




    <div class="modal fade" id="showReviewModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                        @lang('Review By') <span class="name"></span>
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
                        <div class="rating"></div>
                        <small class="datetime text-muted"></small>
                    </div>
                    <h5 class="title  mb-2"></h5>
                    <p class="review"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--sm btn-dark" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection
@push('breadcrumb-plugins')
    <x-search-form placeholder="Title, Order Number, Product Name" />
@endpush
@push('style')
    <style>
        .rating-list {
            display: flex;
            align-items: center;
            gap: 5px;
            max-width: fit-content;
            color: hsl(var(--warning));
            font-weight: 500;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
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
                for (i = 1; i <= 5; i++) {
                    let disabled = i <= rating ? "" : "disabled";
                    html += `<li class="rating-list__item ${ disabled }">
                        <span class = "icon text-warning " > <i class = "fa-${ disabled?'regular':'solid' } fa-star fa-xl" > </i></span>
                        </li>`;
                }

                html += `</ul>`;
                return html;
            }
        })(jQuery);
    </script>
@endpush
