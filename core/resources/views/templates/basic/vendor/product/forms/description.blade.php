<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <label class="form--label required">@lang('Description')</label>
            <textarea name="description" class="form--control nicEdit">{{ old('description', $product?->description) }}</textarea>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2">
                <label class="form--label mb-0">@lang('Short Description')</label>
                <button type="button" class="btn btn--xs btn-outline--base-two addShortDescriptionBtn">
                    <i class="la la-plus">@lang('Add More')</i>
                </button>
            </div>
            <div class="input-group input--group">
                <input type="text" class="form--control" name="short_description[]" value="{{ old('short_description.0', $product?->short_description[0] ?? '') }}" required>

            </div>
        </div>
        <div id="shortDescription">
            @foreach ((old('short_description', $product?->short_description ?? [])) as $shortDescription)
                @continue($loop->first)
                <div class="form-group">
                    <div class="input-group input--group align-items-center">
                        <input type="text" class="form-control form--control" name="short_description[]"
                            value="{{ $shortDescription }}" required>
                        <span class="input-group-text removeShortDescriptionBtn bg-white" role="button">
                            <i class="la la-times fs-18"></i>
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2">
                <label class="form--label">
                    @lang('Design Instruction')
                </label>
                <button type="button" class="btn btn--xs btn-outline--base-two addInstructionBtn">
                    <i class="la la-plus">@lang('Add More')</i>
                </button>
            </div>
            <div class="input-group input--group">
                <input type="text" class="form--control" name="design_instruction[]" value="{{ $product?->design_instruction[0] ?? '' }}">
            </div>
        </div>
        <div id="instructions">
            @foreach ($product?->design_instruction ?? [] as $designInstruction)
                @continue($loop->first)
                <div class="form-group">
                    <div class="input-group input--group align-items-center">
                        <input type="text" class="form-control form--control" name="design_instruction[]"
                            value="{{ $designInstruction }}" required>
                        <span class="input-group-text removeInstructionBtn bg-white" role="button">
                            <i class="la la-times fs-18"></i>
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@push('script-lib')
    <script src="{{ asset('assets/global/js/nicEdit.js') }}"></script>
@endpush
@push('style')
    <style>
        .input-group-text.removeShortDescriptionBtn,
        .input-group-text.removeInstructionBtn {
            color: hsl(var(--danger)/0.7)
        }

        .input-group-text.removeShortDescriptionBtn:hover,
        .input-group-text.removeInstructionBtn:hover,
        .input-group-text.removeShortDescriptionBtn:focus,
        .input-group-text.removeInstructionBtn:focus {
            color: hsl(var(--danger))
        }

        .add-new-product__form .form--label + div .nicEdit-panelContain {
            border-radius: 4px 4px 0 0;
        }

        .add-new-product__form .form-group > div[style*="border-radius"] {
            border-radius: 0 0 4px 4px !important;
        }
    </style>
@endpush
@push('script')
    <script>
        (function($) {
            'use strict';

            $(".nicEdit").each(function(index) {
                $(this).attr("id", "nicEditor" + index);
                new nicEditor({
                    fullPanel: true
                }).panelInstance('nicEditor' + index, {
                    hasPanel: true
                });
            });

            $('.addInstructionBtn').on('click', function() {
                let html = `<div class="form-group">
                    <div class="input-group input--group align-items-center">
                        <input type="text" class="form-control form--control" name="design_instruction[]">
                        <span class="input-group-text removeInstructionBtn bg-white" role="button">
                            <i class="la la-times fs-18"></i>
                        </span>
                    </div>
                </div>`;
                $('#instructions').append(html);
            });

            $(document).on('click', '.removeInstructionBtn', function() {
                $(this).closest('.form-group').remove();
            });

            $('.addShortDescriptionBtn').on('click', function() {
                let html = `<div class="form-group">
                     <div class="input-group input--group align-items-center">
                        <input type="text" class="form-control form--control" name="short_description[]" required>
                        <span class="input-group-text removeShortDescriptionBtn bg-white" role="button">
                            <i class="la la-times fs-18"></i>
                        </span>
                    </div>
                </div>`;
                $('#shortDescription').append(html);
            });

            $(document).on('click', '.removeShortDescriptionBtn', function() {
                $(this).closest('.form-group').remove();
            });
        })(jQuery);
    </script>
@endpush
