@php
    $counterContent = getContent('counter.content', true)?->data_values ?? null;
    $counterElements = getContent('counter.element', false)->pluck('data_values');
@endphp
<section class="counter-section py-60">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-heading mw-100">
                    <h2 class="section-heading__title">{{ __($counterContent->heading ?? null) }}</h2>
                </div>
            </div>
        </div>
        <div class="row align-items-center gy-sm-4 gy-3">
            <div class="col-xl-3">
                <div class="counter-list counterup-item">
                    <div class="row g-xl-4 g-sm-3 g-2">
                        @foreach ($counterElements as $counter)
                            <div class="col-xl-12 col-sm-6">
                                <div class="counter-list__item">
                                    <h2 class="count ">
                                        <span class="odometer" data-odometer-final="{{ $counter->counter_number ?? null }}">
                                        </span>
                                        {{ __($counter->counter_suffix ?? null) }}
                                    </h2>
                                    <p class="title">{{ __($counter->title ?? null) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-xl-9 d-none d-xl-block">
                <div class="counter-thumb">
                    <img src="{{ frontendImage('counter', $counterContent->image ?? '', '1935x1030') }}" alt="@lang('counter')">
                </div>
            </div>
        </div>
    </div>
</section>

@pushOnce('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/odometer.css') }}">
@endPushOnce
@pushOnce('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/odometer.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/viewport.jquery.js') }}"></script>
@endPushOnce
