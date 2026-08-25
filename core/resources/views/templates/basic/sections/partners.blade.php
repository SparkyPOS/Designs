@php
    $partnerContent = getContent('partners.content', true)?->data_values ?? null;
    $partners = getContent('partners.element')->pluck('data_values');
@endphp
<section class="partners-section my-120">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="partners">
                    <h4 class="partners__title">{{ __($partnerContent->title) }}</h4>
                    <div class="partners_wrapper">
                        <div class="partners__list">
                            @foreach ($partners as $partner)
                                <div class="partners__item">
                                    <img src="{{ frontendImage('partners', $partner->image ?? null, '280x70') }}"
                                        alt="@lang('Partner')">
                                </div>
                            @endforeach
                        </div>
                    </div>
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
