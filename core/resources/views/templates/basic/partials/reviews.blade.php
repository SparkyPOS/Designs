<div class="single-review__list">
    @foreach ($reviews as $review)
        <div class="single-review__item">
            @php
                echo showRating($review->rating);
            @endphp
            <h4 class="single-review__title">{{ __($review->title) }}</h4>
            <div class="single-review__content">
                {{ __($review->review) }}
            </div>
            <div class="single-review__reviewer">
                @lang('By')
                <p class="single-review__name">
                    {{ $review->user?->fullname }}
                </p>
                <span class="single-review__date">
                    {{ showDateTime($review->created_at) }}
                </span>
            </div>
        </div>
    @endforeach
</div>

@if (request()->routeIs('product.reviews'))
    <div class="col-md-12">
        @if ($reviews->hasPages())
            {{ paginateLinks($reviews) }}
        @endif
    </div>
@endif
