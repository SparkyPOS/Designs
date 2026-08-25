<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label class="form--label">@lang('Gallery Images')</label>
            <div class="input-images"></div>
            <small class="text--base-two">
                <i class="las la-info-circle"></i>
                @lang('Supported Files:')<strong>.png, .jpg, .jpeg.</strong> @lang(' Image will be resized into ') <strong>{{ getFileSize('product') }}</strong>@lang('px').
            </small>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";

            const toggleFormButtons = () => {
                const fileInput = $('.input-images input[type="file"]')[0];
                const hasFiles = fileInput && fileInput.files.length > 0;

                if (hasFiles) {
                    $('.uploaderCancelBtn').parent().show();
                } else {
                    $('.uploaderCancelBtn').parent().hide();
                }
            };

            const uploader = $('.input-images').fileUploader({
                filesName: 'images',
                maxFiles: 20,
                onSelect: toggleFormButtons,
                onRemove: toggleFormButtons,
                label: `@lang('Drag & drop files here to upload new images')`,
                preloadedInputName: 'old_image_id',
                preloaded: [
                    @foreach ($product->galleryImages as $galleryImage)
                        {
                            id: '{{ $galleryImage->id }}',
                            src: `{{ getImage(getFilePath('product') . '/' . $galleryImage->image) }}`
                        }@if (!$loop->last),@endif
                    @endforeach
                ],
            });

            const clearUploader = () => {
                $('.input-images').find(".delete-file-button").each((i, e) => e.click());
                toggleFormButtons();
            };

            $(document).on('click', '.uploaderCancelBtn', clearUploader);
            toggleFormButtons();

        })(jQuery);
    </script>
@endpush
