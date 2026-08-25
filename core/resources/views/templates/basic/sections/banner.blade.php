@php
    $content = getContent('banner.content', true)?->data_values ?? null;
@endphp

<section class="banner-section mb-120">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 d-flex flex-column">
                <div class="banner-content">
                    <h1 class="banner-content__title">
                        @php
                            $words = explode(' ', $content->heading);
                            if (count($words) >= 2) {
                                $words[1] =
                                    '<span class="title-styled">
                                        <span class="title-styled__top"></span>
                                        <span class="title-styled__bottom"></span>' .
                                        e(trans($words[1])) .
                                    '</span>';
                            }
                            echo implode(' ', $words);
                        @endphp
                    </h1>
                    <p class="banner-content__desc">{{ __($content->subheading) }}</p>
                </div>

                @php
                    $buttonParts = explode('-', $content->button_text, 2);
                    $buttonHtml = e(trim($buttonParts[0]));

                    if (isset($buttonParts[1])) {
                        $buttonHtml .= ' - <i>' . __(e(trim($buttonParts[1]))) . '</i>';
                    }
                @endphp

                <div class="banner-btn">
                    <a href="{{ url($content->button_link) }}" target="_blank" class="banner-btn__link">
                        @php echo $buttonHtml; @endphp
                    </a>
                </div>

                <div class="banner-video">
                    <div class="banner-video__inner">
                        <div class="banner-video__thumb">
                            <img src="{{ frontendImage('banner', $content->video_thumbnail, "650x275") }}"
                                alt="@lang('Image')">
                        </div>

                        <a class="banner-video__popup show-video" href="{{ $content->video_url }}" title="@lang('Video')">
                            <div class="overlay"></div>
                            <span class="text">{{ __('See Video') }}</span>
                            <i class="las la-arrow-circle-right"></i>
                        </a>
                        <span class="banner-video__line">
                        </span>
                    </div>
                    <p class="banner-video__text">
                        {{ __($content?->video_title ?? '') }}
                    </p>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="banner-right">
                    <span class="banner-right__bg"></span>
                    <div class="banner-right__thumb">
                        <img src="{{ frontendImage('banner', $content->image, "710x1400") }}"
                            alt="@lang('banner')">
                    </div>
                    <div class="banner-right__ratting">
                        <div class="user-rate">
                            @for ($i = 0; $i < ($content?->rating_count ?? 5); $i++)
                                <i class="las la-star"></i>
                            @endfor
                        </div>
                        <div class="text">
                            <p class="ratting-count">{{ $content?->rating ?? '' }}/5</p>
                            <p class="ratting-text">{{ __($content?->rating_text ?? '') }}</p>
                        </div>
                    </div>
                    <div class="banner-right__demo">
                        <img src="{{ frontendImage('banner', $content->overlay_image, "420x420") }}"
                            alt="@lang('banner')">
                    </div>
                    <div class="scroll-down">
                        <div class="scroll-down__overlay"></div>
                        <span class="scroll-down__text">@lang('Scroll down to see more')</span>
                        <button class="scroll-down__icon">
                            <i class="las la-arrow-circle-left"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
