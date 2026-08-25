@php
    $testimonialContent = getContent('testimonial.content', true)?->data_values ?? null;
    $testimonials = getContent('testimonial.element')->pluck('data_values') ?? null;
@endphp

<section class="testimonial-section my-120">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-heading mw-100">
                    <h2 class="section-heading__title">{{ __($testimonialContent->heading ?? null) }}</h2>
                </div>
            </div>
        </div>

        <div class="row align-items-center justify-content-between">
            <div class="col-sm-5">
                <div class="testimonial-item__thumb">
                    @foreach ($testimonials ?? [] as $testimonial)
                        <div class="testimonial-item__thumb-list">
                            <img src="{{ frontendImage('testimonial', $testimonial->image ?? '', '1055x1325') }}"
                                class="fit-image rounded-1" alt="@lang('Image')">
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-6  col-sm-7">
                <div class="testimonails-card__list">
                    @foreach ($testimonials ?? [] as $testimonial)
                        <div class="testimonails-card">
                            <h2 class="testimonial-item__desc">{{ __($testimonial->content ?? '') }}</h2>
                            <div class="testimonial-item__details">
                                <p class="testimonial-item__name fw-medium fs-20">{{ __($testimonial->customer ?? '') }}
                                </p>
                                <span
                                    class="testimonial-item__designation fw-medium fs-20">{{ __($testimonial->customer_profession ?? '') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@pushOnce('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/slick.css') }}">
@endPushOnce

@pushOnce('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>
@endpushOnce
