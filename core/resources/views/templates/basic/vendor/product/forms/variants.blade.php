@if (blank($variants))
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card custom--card">
                <div class="card-header">
                    <h3>@lang('Generate  Variants')</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted fs-18">
                        @lang('Variations for this product have not been created based on the current combination of attributes and attribute values. By clicking on the Generate Variations button you are going to create all possible variations.')
                    </p>
                    <div class="d-flex justify-content-end align-items-center gap-2 mt-4">
                        <a href="{{ productBackRoute($isUpdateOrNew, $step, $product->slug ?? null) }}"
                            class="btn btn--sm btn-outline--dark">
                            @lang('Back')
                        </a>
                        <a href="{{ route('vendor.products.generate.variants', $product->id) }}"
                            class="btn btn--sm btn--base">@lang('Generate Variants')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="d-flex justify-content-end mt-3">
                <div class="form-group d-flex justify-content-between align-items-center gap-2">
                    <label for="publishAll" class="text--small">@lang('Publish All')</label>
                    <x-toggle-switch id="publishAll" />
                </div>
            </div>

            <div class="row gy-3">
                @foreach ($variants as $variant)
                    <input type="hidden" name="variants_id[]" value="{{ $variant->id }}">
                    <div class="col-12">
                        <div class="card custom--card printarea--card">
                            <div class="card-header variant-item d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h3 class="card-title mb-0">{{ __($variant->name) }}</h3>
                                <div class="d-flex align-items-center gap-3 variant-item-bottom">
                                    <div class="d-flex align-items-center gap-3 variant-item-bottom mt-3 mb-2">
                                        <label class="mb-0" for="publish{{ $variant->id }}">@lang('Published')</label>
                                        <x-toggle-switch name="is_published[]" value="1" :checked="@$variant->is_published"
                                            class="publishVariant" id="publish{{ $variant->id }}" />
                                    </div>

                                    <a class="toggle-btn expandPrintAreaBtn"
                                        data-bs-toggle="collapse" href="#singleVariant{{ $variant->id }}"
                                        role="button" aria-expanded="false"
                                        aria-controls="singleVariant{{ $variant->id }}">
                                        <i class="las la-angle-up"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="card-body collapse singleVariantItem show"
                                id="singleVariant{{ $variant->id }}">
                                <div class="row gy-4">
                                    <div class="col-md-12">
                                        <div class="row gx-5">
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-xl-6">
                                                        <div class="form-group">
                                                            <label class="form--label"
                                                                for="regular_price{{ $variant->id }}">@lang('Regular Price')</label>
                                                            <div class="input-group input--group">
                                                                <span class="input-group-text">@lang(gs('cur_sym'))</span>
                                                                <input type="number" step="any" min="0" inputmode="decimal"
                                                                    id="regular_price{{ $variant->id }}"
                                                                    class="form-control form--control"
                                                                    name="regular_price[]"
                                                                    value="{{ old('regular_price.' . $loop->index, $variant?->regular_price) }}"
                                                                    required />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6">
                                                        <div class="form-group">
                                                            <label class="form--label"
                                                                for="salePrice{{ $variant->id }}">@lang('Sale Price')</label>
                                                            <div class="input-group input--group">
                                                                <span class="input-group-text">@lang(gs('cur_sym'))</span>
                                                                <input type="number" class="form-control form--control"
                                                                    step="any" min="0" inputmode="decimal" id="salePrice{{ $variant->id }}"
                                                                    name="sale_price[]"
                                                                    value="{{ old('sale_price.' . $loop->index, $variant->sale_price) }}"
                                                                    required />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    @push('script')
        <script>
            (function($) {
                "use strict";
                const publishCheckBox = $('.publishVariant');
                const publishAllCheckBox = $('#publishAll');

                const publishCheckBoxClickHandler = function() {
                    togglePublishAllSwitch();
                }

                const publishAllCheckBoxClickHandler = function() {
                    publishCheckBox.prop('checked', $(this).prop('checked'));
                }

                const togglePublishAllSwitch = () => {
                    const total = publishCheckBox.length;
                    const checked = $('.publishVariant:checked').length;

                    publishAllCheckBox.prop('checked', total == checked);
                }

                publishAllCheckBox.on('click', publishAllCheckBoxClickHandler);
                publishCheckBox.on('click', publishCheckBoxClickHandler);

                togglePublishAllSwitch();

            })(jQuery);
        </script>
    @endpush
@endif
