@php
    $faqContent = getContent('faq.content', true)?->data_values ?? null;
    $faqItems = getContent('faq.element')->pluck('data_values');
@endphp

<section class="faq-section my-120">
    <div class="container">
        <div class="row gy-4 justify-content-center">
            <div class="col-lg-5">
                <div class="section-heading style-left">
                    <h2 class="section-heading__title">{{ __($faqContent->heading ?? '') }}</h2>
                    <p class="section-heading__desc">{{ __($faqContent->subheading ?? '') }}</p>
                </div>

                @if (!request()->routeIs('faq'))
                    <a class="btn btn--base" href="{{ route('faq') }}">
                        @lang('See All FAQs')
                    </a>
                @endif
            </div>
            <div class="col-lg-7">
                <div class="accordion custom--accordion" id="accordionFa">
                    @foreach ($faqItems ?? [] as $faqItem)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $loop->index + 1 }}" aria-expanded="{{ $loop->index == 0 ? 'true' : 'false' }}" aria-controls="collapse-{{ $loop->index + 1 }}">
                                    {{ __($faqItem->question) }}
                                </button>
                            </h2>
                            <div id="collapse-{{ $loop->index + 1 }}" class="accordion-collapse collapse {{ $loop->index == 0 ? 'show' : '' }}" data-bs-parent="#accordionFa">
                                <div class="accordion-body">
                                    <p class="text">
                                        {{ __($faqItem->answer) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
