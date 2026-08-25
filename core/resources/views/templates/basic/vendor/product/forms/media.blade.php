<div class="row gy-4 mb-4">
    <div class="col-sm-5 col-md-4 col-xxl-2">
        <label class="form--label {{ $product?->main_image ? "" : "required" }}">@lang('Main Image')</label>
        <x-frontend-image-uploader name="main_image" :imagePath="$product?->mainImage() ?? null" type="product" :required="($product?->main_image? false : true)" />
    </div>
    <div class="col-sm-7 col-md-8 col-xxl-10">
        @if ($product->product_type == Status::PRODUCT_TYPE_VARIABLE)
            <div class="variant-upload-note">
                <h6 class="title">@lang('Variant-wise Image Upload (Quick Guide)')</h6>
                <ul class="instruction">
                    <li>@lang('Upload images for each automatically created variant section.')</li>
                    <li>@lang('Use clear, high-quality images with exact size') <strong>{{ getFileSize('product') }}
                            @lang('pixels')</strong>, JPG, JPEG or PNG @lang('format and consistent background.')</li>
                    <li>@lang('Include multiple angles: front, back, side, and close-up shots to show product details.')</li>
                    <li>@lang('Ensure each image accurately represents the variant section it belongs to.')</li>
                    <li>@lang('Check that colors, patterns, and textures in the images match the actual product.')</li>
                </ul>
            </div>
        @else
            @include($activeTemplate . '.vendor.product.forms.general_media')
        @endif
    </div>
</div>

@if ($product->product_type == Status::PRODUCT_TYPE_VARIABLE)
    @include($activeTemplate . '.vendor.product.forms.variants_media')
@endif

<div class="card custom--card mt-4">
    <div class="card-body">
        <div class="row align-items-center gy-3">
            <div class="col-lg-5">
                <label for="designerModel" class="form--label mb-1">@lang('3D Product Model') <span class="text-muted">(.glb)</span></label>
                <p class="text-muted mb-0">
                    @lang('Upload a self-contained GLB model for the customer 3D designer. Maximum file size: 50 MB.')
                </p>
            </div>
            <div class="col-lg-7">
                <input type="file" class="form--control form-control" id="designerModel" name="designer_model" accept=".glb,model/gltf-binary">
                @if ($product?->designer_model)
                    <div class="d-flex align-items-center justify-content-between gap-2 mt-2">
                        <small class="text--success text-break">
                            <i class="las la-check-circle"></i>
                            @lang('Current model'): {{ $product->designer_model }}
                        </small>
                        <a href="{{ $product->designerModelUrl() }}" target="_blank" rel="noopener" class="btn btn--sm btn-outline--base">
                            @lang('View GLB')
                        </a>
                    </div>
                @else
                    <small class="text-muted d-block mt-2">
                        @lang('No custom model uploaded. The bundled reference model is currently used.')
                    </small>
                @endif
            </div>
        </div>
        <hr class="my-4">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h6 class="mb-1">@lang('3D Model Settings')</h6>
                <p class="text-muted mb-0">@lang('Adjust the initial model view and front/back artwork placement after uploading a GLB. Rotation values are degrees.')</p>
            </div>
            <span class="badge badge--success">@lang('Live preview')</span>
        </div>
        <x-designer-settings :product="$product" id-prefix="vendor-designer" />
    </div>
</div>

<div class="form-group d-flex align-items-center gap-2 mt-4 mb-0">
    <label for="productPublish" class="form--label mb-0">@lang('Publish product')</label>
    <x-toggle-switch name="published" value="1" :checked="$product?->is_published" id="productPublish" />
</div>


@pushOnce('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/image-uploader.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/designer/designer.css') }}?v={{ @filemtime(base_path('../assets/designer/designer.css')) ?: time() }}">
@endPushOnce

@pushOnce('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/image-uploader.min.js') }}"></script>
@endPushOnce

@push('script')
    <script type="module" src="{{ asset('assets/designer/designer.js') }}?v={{ @filemtime(base_path('../assets/designer/designer.js')) ?: time() }}"></script>
@endpush

@push('style')
    <style>
        @media screen and (max-width: 575px) {
            .image--uploader {
                width: 160px;
            }
        }

        .variant-upload-note {
            margin-top: 28px;
        }

        @media screen and (max-width: 575px) {
            .variant-upload-note {
                margin-top: 0px;
            }
        }

        .variant-upload-note .title {
            margin-bottom: 8px;

        }

        .variant-upload-note .instruction li::before {
            content: '\f05a';
            font-size: 16px;
            font-weight: 900;
            font-family: 'Line Awesome Free';
            margin-right: 8px;
        }

        .variant-upload-note .instruction li {
            font-size: 14px;
            color: hsl(var(--black)/0.5);
            font-weight: 500;
        }

        .variant-upload-note .instruction li strong {
            font-weight: 700;
            color: hsl(var(--black)/0.8);
        }

        .variant-upload-note .instruction li:not(:last-child) {
            margin-bottom: 12px;
        }

        @media screen and (max-width: 575px) {
            .variant-upload-note .instruction li:not(:last-child) {
                margin-bottom: 6px;
            }
        }
    </style>
@endpush
