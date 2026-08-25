@php
    $ctaContent = getContent('cta.content', true)?->data_values ?? null;
@endphp

<section class="cta-section bg--base-two">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-md-6">
                <div class="cta-content">
                    <h2 class="cta-title text-white">{{ __($ctaContent->heading) }}</h2>
                    <div class="cta-btn">
                        <a href="{{ $ctaContent->button_link }}" class="btn btn--base">
                            {{ __($ctaContent->button_text) }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="cta-thumb">
                    <img src="{{ frontendImage('cta', $ctaContent->image, '1275x760') }}" alt="@lang('Cta Thumb')">
                </div>
            </div>
        </div>
    </div>
</section>
