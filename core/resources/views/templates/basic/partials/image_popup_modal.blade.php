<div class="modal fade" id="imagePopupModal" tabindex="-1" aria-labelledby="imagePopupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl  modal-dialog-centered flex-center">
        <div class="modal-content w-fit">
            <button class="modal-close" data-bs-dismiss="modal"><i class="fa-solid fa-times"></i> </button>
            <div class="modal-body flex-center text-center p-0">
                <img class="rounded-2" src="" alt="@lang('Image')">
            </div>
        </div>
    </div>
</div>

@push('style')
    <style>
        .modal-close {
            top: -34px;
            position: absolute;
            z-index: 10;
            color: hsl(var(--white));
            right: -9px;
            padding: 5px;
            font-size: 23px;
            width: fit-content;
        }
        .image-popup {
            cursor: pointer;
        }
    </style>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            const imagePopupModal = $('#imagePopupModal');

            $('.image-popup').on('click', function () {
                let image = $(this).data('image');
                imagePopupModal.find('img').attr('src', image);
                imagePopupModal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
