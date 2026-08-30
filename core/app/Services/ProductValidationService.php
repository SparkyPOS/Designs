<?php

namespace App\Services;

use App\Models\Catalog;
use App\Models\Product;
use App\Constants\Status;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use Illuminate\Support\Facades\Validator;
use App\Rules\SalePriceGreaterThanRegularPrice;

class ProductValidationService {

    private const MAX_PRODUCT_IMAGE_SIZE_KB = 2048 * 1024;

    /**
     * Validate product data.
     *
     * @param $request The request containing product data.
     * @param $product The product being validated.
     *
     * @return mixed The validation result.
     */

    public function validateProduct(Request $request, Product $product) {

        $validationRule = $this->validationRules($request, $product);
        $validationRule['type'] = 'required|string|in:general,description,seo,media,variants';

        return Validator::make($request->all(), $validationRule, $this->customValidationMessages());
    }
    /**
     * Define validation rules for product
     *
     * @return array The validation rules.
     */
    public function validationRules($request, $product = null) {
        $method = $request->type;

        if (method_exists($this, $method)) {
            return $this->{$method}($request, $product) ?? [];
        }

        return [];
    }

    private function general($request, $product) {
        $productTypes = implode(',', [Status::PRODUCT_TYPE_SIMPLE, Status::PRODUCT_TYPE_VARIABLE]);
        return [
            'name'                 => 'required|string',
            "catalog_id"           => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = Catalog::where('id', $value)
                        ->where('status', Status::ENABLE)
                        ->whereHas('catalogCategory', function ($query) {
                            $query->where('status', Status::ENABLE);
                        })
                        ->exists();

                    if (! $exists) {
                        $fail('The selected catalog is invalid or inactive.');
                    }
                }
            ],
            'product_type'         => 'required|in:' . $productTypes,
            'regular_price'        => 'nullable|required_if:product_type,' . Status::PRODUCT_TYPE_SIMPLE . '|numeric|gte:0',
            'sale_price'           => 'nullable|required_if:product_type,' . Status::PRODUCT_TYPE_SIMPLE . '|numeric|gte:0',
            'product_attributes'   => 'nullable|required_if:product_type,' . Status::PRODUCT_TYPE_VARIABLE . '|array|min:1',
            'product_attributes.*' => 'required_with:product_attributes|exists:attributes,id',
            'attribute_values'     => 'nullable|required_with:product_attributes|array|size:' . count($request['product_attributes'] ?? []),
            'attribute_values.*'   => 'required_with:attribute_values|exists:attribute_values,id',
        ];
    }

    private function description($request, $product) {
        return [
            'description'        => ['required', 'string', function ($attribute, $value, $fail) {
                if (trim(strip_tags($value)) === '') {
                    $fail('The ' . str_replace('_', ' ', $attribute) . ' field is required.');
                }
            }],
            'short_description' => 'required|array',
            'short_description.*' => 'required|string',
            'design_instruction' => 'nullable|array',
            'design_instruction.*' => 'nullable|string',
        ];
    }

    private function seo($request, $product) {
        return [
            'meta_title'       => 'nullable|string|max:255',
            'meta_keywords'    => 'nullable|array',
            'meta_description' => 'nullable|string',
        ];
    }

    private function media($request, $product) {
        $validation = [
            'main_image'     => ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png']), 'max:' . self::MAX_PRODUCT_IMAGE_SIZE_KB],
            'designer_model' => ['nullable', 'file', new FileTypeValidate(['glb']), 'max:51200'],
            'designer_form' => 'required|in:' . Product::DESIGNER_FORM_DTG . ',' . Product::DESIGNER_FORM_ENGRAVE,
            'designer_settings' => 'nullable|array',
            'designer_settings.model_scale' => 'nullable|numeric',
            'designer_settings.rotation_x' => 'nullable|numeric',
            'designer_settings.rotation_y' => 'nullable|numeric',
            'designer_settings.rotation_z' => 'nullable|numeric',
            'designer_settings.offset_x' => 'nullable|numeric',
            'designer_settings.offset_y' => 'nullable|numeric',
            'designer_settings.front_x' => 'nullable|numeric',
            'designer_settings.front_y' => 'nullable|numeric',
            'designer_settings.front_width' => 'nullable|numeric',
            'designer_settings.front_height' => 'nullable|numeric',
            'designer_settings.back_x' => 'nullable|numeric',
            'designer_settings.back_y' => 'nullable|numeric',
            'designer_settings.back_width' => 'nullable|numeric',
            'designer_settings.back_height' => 'nullable|numeric',
            'images'         => 'nullable|array|max:20',
            'published'      => 'nullable|in:1,0',
        ];
        if ($product->product_type == Status::PRODUCT_TYPE_VARIABLE) {
            $validation['images.*']   = ['nullable', 'array'];
            $validation['images.*.*'] = ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png']), 'max:' . self::MAX_PRODUCT_IMAGE_SIZE_KB];
        } else {
            $validation['images.*'] = ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png']), 'max:' . self::MAX_PRODUCT_IMAGE_SIZE_KB];
        }
        return $validation;
    }

    private function variants($request, $product) {
        $variants = $product->productVariants;

        return [
            'variants_id'     => 'nullable|array|min:1',
            'variants_id.*'   => 'required|in:' . implode(',', $variants->pluck('id')->toArray()),
            'regular_price'   => 'required|array',
            'regular_price.*' => 'required|numeric|gte:0',
            'sale_price'      => 'required|array',
            'sale_price.*'    => ['required', 'numeric', 'gte:0', new SalePriceGreaterThanRegularPrice()],
            'is_published'    => 'nullable|array',
            'is_published.*'  => 'nullable|in:1,0',
        ];
    }

    /**
     * Define custom validation error messages.
     *
     * @return array An array of custom validation error messages.
     */
    private function customValidationMessages() {
        return [
            'product_attributes.required_if' => 'Product attributes is required if product type is variable',
        ];
    }
}
