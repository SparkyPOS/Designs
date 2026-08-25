<div class="row gy-3 position-sticky">
    @foreach ($variants as $variant)
        @php
            $variantImages = $product?->galleryImages
                ? $product->galleryImages->where('product_variant_id', $variant->id)->values()->all()
                : [];
        @endphp
        <input type="hidden" class="oldImages{{ $variant->id }}" data-images="{{ json_encode($variantImages) }}">
        <input type="hidden" name="variant_id[]" value="{{ $variant->id }}">
        <div class="col-12">
            <div class="card custom--card printarea--card">
                <div class="card-header variant-item d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0">{{ __($variant->name) }}</h4>
                    <div class="d-flex align-items-center gap-3 variant-item-bottom">
                        <a class="toggle-btn expandVariantBtn" data-bs-toggle="collapse"
                            href="#singleVariant{{ $variant->id }}" role="button" aria-expanded="true"
                            aria-controls="singleVariant{{ $variant->id }}">
                            <i class="las la-angle-down"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body collapse singleVariantItem variant-wrapper show"
                    id="singleVariant{{ $variant->id }}">
                    <div class="row gy-4">
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label
                                    class="form--label {{ $variant?->main_image ? '' : 'required' }}">@lang('Main Image')</label>
                                <x-frontend-image-uploader name="variant_main_image[]"
                                    id="variant_main_image{{ $variant->id }}" :imagePath="getImage(getFilePath('product') . '/' . $variant->main_image)" type="product"
                                    :required="$variant?->main_image ? false : true" />
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <div class="form-group">
                                <label class="form--label">@lang('Gallery Images')</label>
                                <div class="input-images input-images{{ $variant->id }}" data-id={{ $variant->id }}>
                                </div>
                                <small class="text--muted">
                                    <i class="las la-info-circle"></i>
                                    @lang('Supported Files:')<strong>.png, .jpg, .jpeg.</strong> @lang(' Image will be resized into ') <strong>{{ getFileSize('product') }}</strong>@lang('px').
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>



@push('script')
    <script>
        (function($) {
            "use strict";

            $('.input-images').each(function() {
                const $inputWrapper = $(this);
                const variantId = $inputWrapper.data('id');
                const oldImages = $(`.oldImages${variantId}`).data('images');
                const cancelBtn = $(`.uploaderCancelBtn${variantId}`);

                const toggleButtons = () => {
                    const fileInput = $inputWrapper.find('input[type="file"]')[0];
                    const hasFiles = fileInput && fileInput.files.length > 0;
                    cancelBtn.parent().toggle(hasFiles);
                };


                $inputWrapper.fileUploader({
                    filesName: `images[${variantId}]`,
                    maxFiles: 20,
                    onSelect: toggleButtons,
                    onRemove: toggleButtons,
                    label: `@lang('Drag & drop files here to upload new images')`,
                    preloadedInputName: `variant_old_image_id[${variantId}]`,
                    preloaded: oldImages.map(image => ({
                        id: image.id,
                        src: `{{ url(getFilePath('product')) }}/thumb_${image.image}`
                    })),
                });

                // Delete all images for this variant
                cancelBtn.on('click', function() {
                    $inputWrapper.find('.delete-file-button').each((i, el) => el.click());
                    toggleButtons();
                });

                toggleButtons();
            });
        })(jQuery);
    </script>
@endpush
