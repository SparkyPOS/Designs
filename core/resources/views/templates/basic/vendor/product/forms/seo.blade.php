<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form--label">@lang('Meta Title')</label>
            <input type="text" name="meta_title" class="form--control"
                value="{{ old('meta_title', $product?->meta_title) }}">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group select2-tokenize">
            <label class="form--label">@lang('Meta keywords')</label>
            <select name="meta_keywords[]" class="form--control select2 select2-auto-tokenize meta_keywords-field"
                multiple="multiple">
                @if ($product?->meta_keywords)
                    @foreach ($product->meta_keywords as $option)
                        <option value="{{ $option }}" selected>{{ __($option) }}</option>
                    @endforeach
                @endif
            </select>
            <small class="form-text text-muted text--base-two">
                <i class="las la-info-circle"></i>
                @lang('Type , as separator or hit enter among keywords')
            </small>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group">
            <label class="form--label">@lang('Meta Description')</label>
            <textarea name="meta_description" class="form--control">{{ old('meta_description', $product?->meta_description) }}</textarea>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            'use strict';

            $.each($('.select2-auto-tokenize'), function() {
                $(this)
                    .wrap(`<div class="position-relative"></div>`)
                    .select2({
                        tags: true,
                        tokenSeparators: [','],
                        dropdownParent: $(this).parent()
                    });
            });

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .select2-tokenize .select2-selection__choice {
            margin-bottom: 0 !important;

        }

        .select2-wrapper .select2+.select2-container .select2-selection--multiple .select2-search__field {
            padding: 0px;
            height: 42px;
        }
    </style>
@endpush
