<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form--label">@lang('Name')</label>
            <input type="text" class="form--control" name="name" value="{{ old('name', $product?->name) }}" required>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label class="form--label">@lang('Catalog')</label>
            <select class="form--control select2" name="catalog_id" required>
                <option value="">@lang('Select Catalog')</option>
                @foreach ($catalogs as $catalog)
                    <option value="{{ $catalog->id }}" class="ms-3" @selected(old('catalog_id', $product?->catalog_id) == $catalog->id)> {{ __($catalog->name) }} - {{ __($catalog?->catalogCategory?->name) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label class="form--label" for="product_type">@lang('Type')</label>
            <select name="product_type" class="form--control select2" id="product_type"
                data-minimum-results-for-search="-1" required>
                <option value="{{ Status::PRODUCT_TYPE_SIMPLE }}" @selected(old('product_type', $product?->product_type) == Status::PRODUCT_TYPE_SIMPLE)>@lang('Simple Product')
                </option>
                <option value="{{ Status::PRODUCT_TYPE_VARIABLE }}" @selected(old('product_type', $product?->product_type) == Status::PRODUCT_TYPE_VARIABLE)>@lang('Variable Product')
                </option>
            </select>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label class="form--label">@lang('Attributes')</label>
            <select name="product_attributes[]" class="form--control select2" data-minimum-results-for-search="-1"
                multiple @disabled(old('product_type', $product?->product_type) != Status::PRODUCT_TYPE_VARIABLE)>
                @foreach ($attributes as $attribute)
                    <option value="{{ $attribute->id }}">{{ __($attribute->name) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div
        class="col-lg-6 price {{ old('product_type', $product?->product_type) == Status::PRODUCT_TYPE_VARIABLE ? 'd-none' : null }}">
        <div class="form-group">
            <label class="form--label" for="regular_price">@lang('Regular Price')</label>
            <div class="input-group input--group">
                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                <input type="number" class="form--control form-control" name="regular_price"
                    value="{{ old('product_type', $product?->product_type) == Status::PRODUCT_TYPE_SIMPLE ? old('regular_price', $product?->regular_price) : null }}"
                    id="regular_price" @required(old('product_type', $product?->product_type) != Status::PRODUCT_TYPE_VARIABLE)>
            </div>
        </div>
    </div>
    <div
        class="col-lg-6 price {{ old('product_type', $product?->product_type) == Status::PRODUCT_TYPE_VARIABLE ? 'd-none' : null }}">
        <div class="form-group">
            <label class="form--label" for="sale_price">@lang('Sale Price')</label>
            <div class="input-group input--group">
                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                <input type="number" class="form--control form-control" name="sale_price"
                    value="{{ old('product_type', $product?->product_type) == Status::PRODUCT_TYPE_SIMPLE ? old('sale_price', $product?->sale_price) : null }}"
                    id="sale_price" @required(old('product_type', $product?->product_type) != Status::PRODUCT_TYPE_VARIABLE)>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="attribute-values-area row"></div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            'use strict';
            const attributeSelectElement = $('[name="product_attributes[]"]');
            const productTypeElement = $('[name="product_type"]');
            const priceAreaElement = $('.price');
            const productAttributeValues = JSON.parse(`@php echo json_encode($attributeValues); @endphp`);
            const attributes = JSON.parse(`@php echo json_encode($attributes); @endphp`);
            const productAttributes = JSON.parse(`@php echo json_encode(old('product_attributes', $productAttributes)); @endphp`);

            productTypeElement.on('change', function() {
                let type = $(this).val();
                $('.attribute-values-area').html("");
                priceAreaElement.find('input').val("");
                let disable = true;
                let required = false;
                let requiredMethod = 'removeClass';
                let priceMethod = 'removeClass';
                let priceRequiredMethod = 'addClass';
                if (type === `{{ Status::PRODUCT_TYPE_VARIABLE }}`) {
                    disable = false;
                    required = true;
                    requiredMethod = 'addClass';
                    priceMethod = 'addClass';
                    priceRequiredMethod = 'removeClass';
                }

                attributeSelectElement.val("").trigger("change").prop('disabled', disable).prop('required',
                    required);
                attributeSelectElement.closest('.form-group').find('label')[requiredMethod]('required');
                priceAreaElement[priceMethod]('d-none');
                priceAreaElement.find('label')[priceRequiredMethod]('required');
                priceAreaElement.find('input').prop('required', !required);
            });

            attributeSelectElement.val(productAttributes).trigger("change");

            const selectedAttribute = attributeSelectElement.find(':selected');

            selectedAttribute.each(function(index, element) {
                let id = $(element).val();
                let values = productAttributeValues[id].map(val => Number(val));;

                attributes.forEach(function(attribute) {
                    if (attribute.id == id) {
                        values = (values == undefined) ? [] : values;
                        addNewAttribute(id, attribute.name, attribute.type, attribute.attribute_values,
                            values)
                    }
                });
            });


            attributeSelectElement.on('change', function(e) {
                let attributeIds = $(this).val();
                (attributeIds || []).forEach(function(id) {
                    if (!$('.attribute-values-area').find(`[name="attribute_values[${id}][]"]`)
                        .length) {
                        attributes.forEach(function(attribute) {
                            if (attribute.id == id) {
                                addNewAttribute(id, attribute.name, attribute.type, attribute
                                    .attribute_values)
                            }
                        });
                    }
                });

                $('.attribute-values-area').find('select').each(function() {
                    if (!attributeIds.includes($(this).data('id').toString())) {
                        $(this).closest('.col-lg-6').remove();
                    }
                });

            });

            // select2-wrapper`

        function addNewAttribute(id, name, type, values, selectedValues = []) {
            // Build HTML
            let html = `<div class="col-lg-6">
                    <div class="form-group">
                        <label class="form--label required">${name}</label>
                        <select name="attribute_values[${id}][]" data-id="${id}" class="form--control select2" data-minimum-results-for-search="-1" multiple required>`;
                    values.forEach(function(value) {
                        html += `<option value="${value.id}" ${selectedValues.includes(Number(value.id)) ? "selected" : ""}>
                            ${getTypeValue(type, value)} ${value.name}
                        </option>`;
                    });
                    html += `</select>
                    </div>
                </div>`;

            let $element = $(html).appendTo('.attribute-values-area');

            let $select = $element.find(".select2");
            $select.wrap('<div class="select2-wrapper"></div>');
            $select.select2({
                dropdownParent: $select.closest(".select2-wrapper")
            });

        }


        function getTypeValue(type, values) {
            if (type == `{{ Status::ATTRIBUTE_TYPE_TEXT }}`) {
                return `<span>${values.value}</span>`
            } else if (type == `{{ Status::ATTRIBUTE_TYPE_COLOR }}`) {
                return `<span class="p-3" style="background-color: #${values.value}"></span>`
                } else {
                    return "";
                }
            }

        })(jQuery);
    </script>
@endpush
