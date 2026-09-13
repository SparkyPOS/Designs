@extends('Template::layouts.frontend')

@section('content')
    <div class="product-breadcrumb mt-3">
        <div class="container">
            <div class="product-breadcrumb__wrapper">
                <a href="{{ route('product.all.catalogs') }}" class="product-breadcrumb__link"> @lang('All Catalogs') </a>
                <a href="{{ route('product.catalogs', $product?->catalog->catalogCategory?->slug) }}" class="product-breadcrumb__link">
                    {{ __($product?->catalog->catalogCategory?->name) }} </a>
                <a href="{{ route('product.list', [$product?->catalog->catalogCategory?->slug, $product?->catalog?->slug]) }}" class="product-breadcrumb__link"> {{ __($product?->catalog?->name) }} </a>
                <a href="javascript:void(0)" class="product-breadcrumb__link"> {{ __($product->name) }} </a>
            </div>
        </div>
    </div>

    <div class="product-info my-60">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="product-thumb">
                        <div class="product-thumb-preview">
                            <img class="xzoom5" src="{{ $product->mainImage(true) }}" xoriginal="{{ $product->mainImage() }}" alt="@lang('Image')">
                        </div>
                        <div class="product-thumb-list" id="details-thumb-list">
                            <div class="product-thumb-item imageId">
                                <img class="product-thumb-item-img" src="{{ $product->mainImage(true) }}" alt="@lang('Image')" data-original="{{ $product->mainImage() }}">
                            </div>
                            @foreach ($images as $image)
                                <div class="product-thumb-item imageId imageId{{ $image->id }}">
                                    <img class="product-thumb-item-img" src="{{ getImage(getFilePath('product') . '/thumb_' . $image->image) }}" alt="@lang('Image')" data-original="{{ getImage(getFilePath('product') . '/' . $image->image) }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="product-info__basic">
                        <h2 class="product-info__title mb-0">{{ __($product->name) }}</h2>
                        <p class="product-info__supplier">
                            @lang('By') <a href="{{ route('product.vendor', $product?->vendor?->username) }}" class="product-info__supplier-name">{{ __($product?->vendor?->company_name) }}</a>
                        </p>
                    </div>

                    @if (!blank($product->short_description ?? []))
                        <div class="product-info__short-desc">
                            @foreach ($product->short_description ?? [] as $shortDescription)
                                <div class=" product-info__short-desc-info">
                                    <span class="product-info__short-desc-icon">
                                        <i class="fa-solid fa-caret-right"></i>
                                    </span>
                                    <span class="product-info__short-desc-icon">
                                        {{ __($shortDescription) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($product->product_type == Status::PRODUCT_TYPE_VARIABLE && $product->attributes->count())
                        <div class="product-attribute position-relative mt-4">
                            @foreach ($product->attributes as $attribute)
                                @php
                                    $attributeValues = $product->attributeValues->where('attribute_id', $attribute->id);
                                @endphp

                                <div class="attributeValueArea">
                                    <p class="attribute-name mb-1">{{ __($attribute?->name) }}:</p>
                                    <div class="attribute-value-wrapper">
                                        @foreach ($attributeValues as $attributeValue)
                                            @php
                                                $data = [
                                                    'attribute_id' => $attribute->id,
                                                    'value_id' => $attributeValue->id,
                                                    'type' => $attribute->type,
                                                ];
                                            @endphp
                                            <label for="{{ $attributeValue->id }}" class="attribute-value">
                                                @if ($attribute->type == Status::ATTRIBUTE_TYPE_TEXT)
                                                    <span class="text-attribute text-center">{{ $attributeValue->name }}</span>
                                                @elseif($attribute->type == Status::ATTRIBUTE_TYPE_COLOR)
                                                    <span class="color-attribute colorAttribute w-100 h-100" data-color="{{ $attributeValue->value }}" style="background:#{{ $attributeValue->value }}"></span>
                                                @else
                                                    <span class="image-attribute bg--img" data-background="{{ getImage(getFilePath('attribute') . '/' . $attributeValue->value) }}"></span>
                                                @endif
                                                <input type="radio" class="attributeBtn" name="attribute-value{{ $attribute->id }}" id="{{ $attributeValue->id }}" data-attribute='@json($data)'>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="product-info__price">
                        <h2 class="product-info__price-amount" id="salePrice">
                            {{ showAmount($product->sale_price) }}
                        </h2>
                        @if ($product->sale_price != $product->regular_price)
                            <del class="product-info__price-text" id="regularPrice">
                                {{ showAmount($product->regular_price) }}
                            </del>
                        @else
                            <del class="product-info__price-text d-none" id="regularPrice"></del>
                        @endif
                    </div>
                    @if ($product->usesDesigner())
                        <a href="{{ $product->product_type != Status::PRODUCT_TYPE_VARIABLE ? route('product.design', $product->slug) : 'javascript:void(0)' }}" class="w-100 btn btn--lg btn--base d-inline-flex align-items-center justify-content-between fs-20 mt-4" id="designBtn">
                            <span class="text">@lang('Start Design')</span>
                            <span class="icon">
                                <i class="fa-solid fa-arrow-right"></i>
                            </span>
                        </a>
                    @else
                        <form action="{{ route('product.buy.now') }}" method="post" id="buyNowForm">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="variant_id" value="" id="buyNowVariantId">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-100 btn btn--lg btn--base d-inline-flex align-items-center justify-content-between fs-20 mt-4 {{ $product->product_type == Status::PRODUCT_TYPE_VARIABLE ? 'disabled' : '' }}" id="designBtn">
                                <span class="text">@lang('Buy Now')</span>
                                <span class="icon">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="product-info__details">
                        <div class="product-info__details-tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="btn  h2 active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc-tab-pane" type="button" role="tab" aria-controls="desc-tab-pane" aria-selected="true">@lang('Description')</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="btn h2" id="design-tab" data-bs-toggle="tab" data-bs-target="#design-tab-pane" type="button" role="tab" aria-controls="design-tab-pane" aria-selected="true">@lang('Design Instruction')</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="btn h2" id="review-tab" data-bs-toggle="tab" data-bs-target="#review-tab-pane" type="button" role="tab" aria-controls="review-tab-pane" aria-selected="true">@lang('Reviews')
                                        ({{ $product->reviews->count() }})</button>
                                </li>
                            </ul>
                        </div>
                        <div class="product-info__desc">
                            <div class="tab-content">
                                <div class="tab-pane fade show active " id="desc-tab-pane" role="tabpanel" aria-labelledby="desc-tab" tabindex="0">
                                    <div class="my-3">
                                        @php
                                            echo $product->description;
                                        @endphp
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="design-tab-pane" role="tabpanel" aria-labelledby="design-tab" tabindex="0">
                                    <div class="my-3">
                                        @php
                                            $designInstructions = $product->design_instruction ?? ($product?->vendor?->design_instruction ?? []);
                                        @endphp
                                        @foreach ($designInstructions as $designInstruction)
                                            <p>{{ __($designInstruction) }}</p>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="tab-pane fade " id="review-tab-pane" role="tabpanel" aria-labelledby="review-tab" tabindex="0">
                                    <div class="all-review {{ blank($product->reviews) ? 'justify-content-center' : '' }}">
                                        <div class="all-review__number">
                                            <p class="average-rating">
                                                {{ $product->averageRating() }} <span class="total_number">/5</span>
                                            </p>
                                            <div class="total-review">
                                                @php
                                                    echo showRating($product->averageRating());
                                                @endphp
                                            </div>
                                            <div class="total-review__text">
                                                <span class="total_number">{{ $product->reviews->count() }}</span>
                                                <span class="total_text">@lang('Reviews')</span>
                                            </div>
                                        </div>
                                        @php
                                            $productReviews = $product->reviewCountsByStars();
                                        @endphp
                                        <div class="all-review__list">
                                            <div class="review-item">
                                                <div class="review-item__star">
                                                    @php
                                                        echo showRating(5);
                                                    @endphp
                                                </div>
                                                <div class="review-item__content">
                                                    <span class="review-item__line" style="--fill:{{ $productReviews['percentage'] ?? 0 }}%;"></span>
                                                    <span class="review-item__date">{{ $productReviews['count'] ?? 0 }}</span>
                                                </div>
                                            </div>
                                            <div class="review-item">
                                                <div class="review-item__star">
                                                    @php
                                                        echo showRating(4);
                                                    @endphp
                                                </div>
                                                <div class="review-item__content">
                                                    <span class="review-item__line" style="--fill:{{ $productReviews['percentage'] ?? 0 }}%;"></span>
                                                    <span class="review-item__date">{{ $productReviews['count'] ?? 0 }}</span>
                                                </div>
                                            </div>
                                            <div class="review-item">
                                                <div class="review-item__star">
                                                    @php
                                                        echo showRating(3);
                                                    @endphp
                                                </div>
                                                <div class="review-item__content">
                                                    <span class="review-item__line" style="--fill:{{ $productReviews['percentage'] ?? 0 }}%;"></span>
                                                    <span class="review-item__date">{{ $productReviews['count'] ?? 0 }}</span>
                                                </div>
                                            </div>
                                            <div class="review-item">
                                                <div class="review-item__star">
                                                    @php
                                                        echo showRating(2);
                                                    @endphp
                                                </div>
                                                <div class="review-item__content">
                                                    <span class="review-item__line" style="--fill:{{ $productReviews['percentage'] ?? 0 }}%;"></span>
                                                    <span class="review-item__date">{{ $productReviews['count'] ?? 0 }}</span>
                                                </div>
                                            </div>
                                            <div class="review-item">
                                                <div class="review-item__star">
                                                    @php
                                                        echo showRating(1);
                                                    @endphp
                                                </div>
                                                <div class="review-item__content">
                                                    <span class="review-item__line" style="--fill:{{ $productReviews['percentage'] ?? 0 }}%;"></span>
                                                    <span class="review-item__date">{{ $productReviews['count'] ?? 0 }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        @if (!blank($product->reviews))
                                            <a href="{{ route('product.reviews', $product->slug) }}" class="btn btn--xs btn-outline--base-two ms-auto align-self-start" target="_blank">@lang('View All')</a>
                                        @endif
                                    </div>
                                    @if (!blank($product->reviews))
                                        <div class="single-review">
                                            @include($activeTemplate . '.partials.reviews', [
                                                'reviews' => $product->reviews->take(8),
                                            ])
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if (!blank($otherProducts))
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="sold-together">
                                <h2 class="sold-together__title">{{ __($otherProductsTitle) }}</h2>
                                <div class="sold-together__list-wrapper">
                                    <div class="sold-together__list">
                                        @include($activeTemplate . '.partials.product', [
                                            'products' => $otherProducts,
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@pushOnce('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/xzoom.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/magnific-popup.css') }}">
@endPushOnce

@pushOnce('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/xzoom.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/magnific-popup.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/setup.js') }}"></script>
@endpushOnce

@push('script')
    <script>
        (function($) {
            'use strict';
            const totalAttribute = "{{ count($product?->attributes ?? []) }}";
            let attributeValues = [];
            const designBtn = $('#designBtn');
            const usesDesigner = "{{ $product->usesDesigner() ? 1 : 0 }}" == "1";
            const buyNowVariantInput = $('#buyNowVariantId');
            const desingUrl = "{{ route('product.design', $product->slug) }}";
            const allImages = JSON.parse(`@php echo $images; @endphp`);
            const productVariants = JSON.parse(`@php echo $product->productVariants()->select('attribute_values', 'id', 'sale_price', 'regular_price', 'main_image', 'is_published')->get(); @endphp`);
            let currentImageVariant = "";

            $('.attributeBtn').on('click', function() {
                let data = $(this).data('attribute');
                let existingIndex = attributeValues.findIndex(item => item.attribute_id === data.attribute_id);

                if (existingIndex !== -1) {
                    attributeValues[existingIndex] = data;
                } else {
                    attributeValues.push(data);
                }

                if (totalAttribute == attributeValues.length) {
                    let valueIdString = attributeValues.map(item => item.value_id).join(',');
                    let variant = getImageVariantId();

                    updateVariantImages(variant);
                    if (variant.is_published == '{{ Status::DISABLE }}') {
                        notify('error', '@lang('This product variant is currently unavailable.')');
                        disableActionBtn();
                        return;
                    } else {
                        if (usesDesigner) {
                            designBtn.removeClass('disabled').attr('href', `${desingUrl}/${variant.id}`);
                        } else {
                            designBtn.removeClass('disabled');
                            buyNowVariantInput.val(variant.id);
                        }
                    }

                } else {
                    disableActionBtn();
                }
            });

            function disableActionBtn() {
                designBtn.addClass('disabled');
                if (usesDesigner) {
                    designBtn.attr('href', `javascript:void(0)`);
                } else {
                    buyNowVariantInput.val('');
                }
            }

            designBtn.on('click', function(e) {
                if (totalAttribute != attributeValues.length) {
                    e.preventDefault();
                    notify('error', `@lang('Please select variant')`);
                    disableActionBtn();
                    return;
                }
            });

            function getImageVariantId() {
                let selectedIds = attributeValues.map(item => item.value_id);
                selectedIds.sort((a, b) => a - b);

                let matchedVariant = null;

                for (let variant of productVariants) {
                    let variantValues = [...variant.attribute_values].sort((a, b) => a - b);
                    if (JSON.stringify(variantValues) === JSON.stringify(selectedIds)) {
                        matchedVariant = variant;
                        $('#salePrice').text(`{{ gs('cur_sym') }}${variant.sale_price} {{ gs('cur_text') }}`);
                        $('#regularPrice').text(`{{ gs('cur_sym') }}${variant.regular_price} {{ gs('cur_text') }}`);
                        if (variant.sale_price != variant.regular_price) {
                            $('#regularPrice').removeClass('d-none');
                        } else {
                            $('#regularPrice').addClass('d-none');
                        }
                        break;
                    }
                }
                return matchedVariant;
            }

            function updateVariantImages(variant) {
                if (currentImageVariant != variant.id) {
                    currentImageVariant = variant.id;

                    const images = allImages.filter(item => item.product_variant_id == variant.id);

                    $('.product-thumb-list').find('.slick-slide').addClass('d-none');

                    images.forEach(element => {
                        $('.imageId' + element.id)
                            .closest('.slick-slide')
                            .removeClass('d-none');
                    });

                    const baseImageUrl = "{{ url(getFilePath('product')) }}";

                    $('.product-thumb-preview img')
                        .attr('src', baseImageUrl + '/thumb_' + variant.main_image)
                        .attr('xoriginal', baseImageUrl + '/' + variant.main_image);

                }
            }


            $('.product-thumb-item-img').on('click', function() {
                const thumbSrc = $(this).attr('src');
                const originalSrc = $(this).data('original');

                $('.product-thumb-preview img')
                    .attr('src', thumbSrc)
                    .attr('xoriginal', originalSrc);
            });


        })(jQuery);
    </script>
@endpush
