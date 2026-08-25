@php
    $catalogContent = getContent('gallery.content', true)?->data_values ?? null;
@endphp
<section class="gallery-section mb-120">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="gallery-section__title">{{ __($catalogContent->heading ?? "") }}</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="gallery-thumb">
                    <div class="gallery-thumb__item">
                        <img src="{{ frontendImage("gallery", ($catalogContent->image_one ?? ""), "1065x1505") }}" alt="@lang('gallery-thumb')">
                    </div>
                    <div class="gallery-thumb__item">
                        <img src="{{ frontendImage("gallery", ($catalogContent->image_two ?? ""), "1500x730") }}" alt="@lang('gallery-thumb')">
                    </div>
                    <div class="gallery-thumb__item">
                        <img src="{{ frontendImage("gallery", ($catalogContent->image_three ?? ""), "735x735") }}" alt="@lang('gallery-thumb')">
                    </div>
                    <div class="gallery-thumb__item">
                        <img src="{{ frontendImage("gallery", ($catalogContent->image_four ?? ""), "735x735") }}" alt="@lang('gallery-thumb')">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
