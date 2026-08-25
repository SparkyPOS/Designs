@php
    $content = getContent('cta_two.content', true);
@endphp

<section class="cta py-60">
    <div class="container">
        <div class="cta-card">
            <img class="cta-card__thumb" src="{{ frontendImage('cta_two', $content?->data_values?->image) }}" alt="@lang('image')">
            <div class="cta-card__content">
                <div class="cta-card__headings">
                    <h2 class="cta-card__title">{{ __($content?->data_values?->heading ?? '') }}</h2>
                    <p class="cta-card__desc">{{ __($content?->data_values?->subheading ?? '') }}</p>
                </div>
                <div class="cta-card__btn-wrapper ">
                    <a class="btn btn--dark" href="{{ $content?->data_values?->button_link ?? 'javascript:void(0)' }}">{{ __($content?->data_values?->button_text ?? '') }}</a>
                    <i>{{ __($content?->data_values?->button_title ?? '') }}</i>
                </div>
            </div>
        </div>
    </div>
</section>
