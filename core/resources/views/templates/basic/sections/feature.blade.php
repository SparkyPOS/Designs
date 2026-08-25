@php
    $featureContent = getContent('feature.content', true)?->data_values ?? null;
    $features = getContent('feature.element', orderById: 'id')->pluck('data_values');
@endphp

<section class="features-section my-120">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-heading style-left">
                    <h2 class="section-heading__title">{{ __($featureContent->heading) }}</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-between animated-thing-3 position-relative">
            <div class="col-xl-6 col-lg-6">
                <div class="position-md-relative">
                    <div class="features-list">
                        @foreach ($features as $feature)
                            <div class="features-item left-menu-item {{ $loop->index == 0 ? 'active' : '' }}">
                                <div class="features-item__inner">
                                    <div class="features-item__icon">
                                        {{ $loop->index + 1 }}
                                    </div>
                                    <div class="features-item__content">
                                        <h3 class="features-item__title">
                                            {{ __($feature->title) }}
                                        </h3>
                                        <p class="features-item__desc">
                                            {{ __($feature->description) }}
                                        </p>
                                    </div>
                                </div>

                                <span class="left-menu-item-bar">
                                    <span class="barline"></span>
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="features-btn position-absolute">
                        <a href="{{ url($featureContent->button_one_link) }}" target="_blank"
                            class="btn btn--base">{{ __($featureContent->button_one_text) }}</a>
                        <a href="{{ url($featureContent->button_two_link) }}"
                            class="mt-2 d-block fw-semibold underline">{{ __($featureContent->button_two_text) }}</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-5 col-lg-6">
                <div class="features-section__thumb-list right-tab-content">
                    @foreach ($features as $featureImage)
                        <div class="features-section__thumb-item tab-item active"
                            style="{{ $loop->index === 0 ? '' : 'display: none;' }}">
                            <img src="{{ frontendImage('feature', $featureImage->image, '860x1080') }}"
                                alt="@lang('Feature Thumbnail')">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@pushOnce('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/animatedTab22.js') }}"></script>
@endPushOnce
