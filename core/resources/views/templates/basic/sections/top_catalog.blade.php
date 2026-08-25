@php
    $catalogContent = getContent('top_catalog.content', true)?->data_values ?? null;
    $catalogs = App\Models\Catalog::active()->with('catalogCategory')->withCount('product')
    ->orderBy('product_count', 'desc')->take(20)->get();
@endphp

<section class="category-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-heading mw-100">
                    <h2 class="section-heading__title">
                        {{ __($catalogContent?->heading) }}
                    </h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="category-list">
                    @foreach($catalogs as $catalog)
                        <a href="{{ route('product.list', [$catalog?->catalogCategory?->slug, $catalog->slug]) }}" class="category-list__item">
                            <img class="category-list__thumb" src="{{ getImage(getFilePath('catalog') . '/' . $catalog->image, getFileSize('catalog')) }}"
                                alt="@lang('Catalog')">
                            <p class="category-list__title">{{ __($catalog->name) }}</p>
                        </a>
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
