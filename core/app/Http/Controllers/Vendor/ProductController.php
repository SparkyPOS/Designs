<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Catalog;
use App\Models\Product;
use App\Constants\Status;
use App\Models\Attribute;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\ProductPrintArea;
use App\Http\Controllers\Controller;
use App\Services\ProductValidationService;
use Illuminate\Database\Eloquent\Collection;

class ProductController extends Controller {
    public function index() {
        $pageTitle = "Products";
        $products  = Product::searchable(['name'])->where('vendor_id', authVendorId())->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::vendor.product.index', compact('pageTitle', 'products'));
    }

    public function create($slug = null, $step = null) {
        $product = null;
        if ($slug) {
            $product = Product::where('slug', $slug)->where('vendor_id', authVendorId())->first();
        }
        return $this->productForm("Add New Product", "new", $product, $step);
    }

    /**
     * Show the form to edit an existing digital product.
     *
     * @param int $id Product ID
     * @return \Illuminate\View\View
     */
    public function edit($slug, $step = Status::STEP_GENERAL) {
        $product = Product::where('slug', $slug)->where('vendor_id', authVendorId())->firstOrFail();
        return $this->productForm("Edit Product", 'update', $product, $step);
    }

    /**
     * Common method to set up data for rendering the product form.
     *
     * @param string $pageTitle Page title for the form
     * @param Product|null $product Product (for editing)
     * @return \Illuminate\View\View
     */
    public function productForm($pageTitle, $isUpdateOrNew, $product = null, $step = null) {
        $catalogs = Catalog::active()
            ->with(['catalogCategory' => function ($query) {
                return $query->active()->orderBy('name');
            }])
            ->whereHas('catalogCategory', function ($query) {
                $query->active();
            })
            ->orderBy('name')
            ->get();
        $attributes        = Attribute::with('attributeValues')->active()->where('vendor_id', authVendorId())->get();
        $productAttributes = [];
        $attributeValues   = [];
        $step              = $step ?? Status::STEP_GENERAL;

        // Print-area setup is no longer part of the product wizard. Keep old
        // bookmarks safe by taking users to the final (media) step instead.
        if ($step === Status::STEP_PRINT) {
            if (!$product) {
                return to_route('vendor.products.index');
            }

            return to_route($this->getRedirectUrl($isUpdateOrNew), [$product->slug, Status::STEP_MEDIA]);
        }

        $allowedSteps = [
            Status::STEP_GENERAL,
            Status::STEP_DESCRIPTION,
            Status::STEP_SEO,
            Status::STEP_VARIANTS,
            Status::STEP_MEDIA,
        ];

        abort_unless(in_array($step, $allowedSteps, true), 404);

        if ($product && $product->attributes->count()) {
            $productAttributes = $product->attributes->pluck('id');
            $attributeValues   = $product->attributeValues->groupBy('attribute_id');
            $attributeValues   = $attributeValues->map->pluck('pivot.attribute_value_id')->all();
        }
        $variants = $product->productVariants ?? [];
        return view('Template::vendor.product.forms.form', compact('pageTitle', 'catalogs', 'product', 'attributes', 'attributeValues', 'productAttributes', 'step', 'isUpdateOrNew', 'variants'));
    }

    /**
     * Adjust the stock of a product based on the form data.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $productId Product ID
     * @return void
     */
    public function store(Request $request, $id = NULL) {
        $isUpdate = $id ? true : false;
        if ($isUpdate) {
            $product = Product::where('vendor_id', authVendorId())->findOrFail($id);
        } else {
            $product            = new Product();
            $product->vendor_id = authVendorId();
        }

        // Reject legacy step-six submissions without changing or deleting any
        // existing print-area records.
        if ($request->type === Status::STEP_PRINT) {
            $notify[] = ['info', 'Print area setup is no longer part of the product form'];
            if (!$product->exists) {
                return to_route('vendor.products.index')->withNotify($notify);
            }

            return to_route('vendor.products.edit', [$product->slug, Status::STEP_MEDIA])->withNotify($notify);
        }

        $validationService = new ProductValidationService();
        $validator         = $validationService->validateProduct($request, $product);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        return $this->{$request->type . "Store"}($request, $product);
    }

    private function generalStore($request, $product) {
        $product->name          = $request->name;
        $product->catalog_id    = $request->catalog_id;
        $product->product_type  = $request->product_type;
        $product->regular_price = $request->regular_price;
        $product->sale_price    = $request->sale_price;
        $product->slug          = createUniqueSlug($request->name, Product::class, $product?->id);
        $product->save();

        $this->ensureDefaultPrintArea($product);

        $needAttributeAdjustment = $this->isAttributeAdjustmentNeeded($request, $product);

        if ($needAttributeAdjustment) {
            $productAttributes = ($product->product_type == Status::PRODUCT_TYPE_VARIABLE) ? $request->product_attributes : [];
            $attributeValues   = ($product->product_type == Status::PRODUCT_TYPE_VARIABLE) ? $request->attribute_values : [];

            $this->adjustProductAttributes($productAttributes, $product, $request->isUpdateOrNew);

            $attributeValues = array_merge(...$attributeValues);
            $this->adjustProductAttributeValues($attributeValues, $product, $request->isUpdateOrNew);

            $this->adjustProductVariants($product->id);
        }

        $notify[] = ['success', "Product general info saved successfully"];
        if ($request->isUpdateOrNew == 'edit') {
            $notify[] = ['success', "Product general info updated successfully"];
        }

        return to_route($this->getRedirectUrl($request->isUpdateOrNew), [$product->slug, Status::STEP_DESCRIPTION])->withNotify($notify);
    }

    /**
     * Adjust the attributes of a product.
     *
     * This function allows you to modify the attributes associated with a product.
     * You can either update the existing attributes or attach new attributes.
     *
     * @param array   $attributes An array containing the IDs of the attributes to be associated with the product.
     * @param Product $product    An instance of the Product class representing the product whose attributes are to be adjusted.
     * @return void
     */
    private function adjustProductAttributes(array $attributes, Product $product): void {
        $product->attributes()->sync($attributes);
    }

    /**
     * Adjust the attribute values of a product.
     *
     * This function allows you to modify the attribute values associated with a product.
     * You can either update the existing attribute values or attach new ones.
     *
     * @param array   $attributeValues An array containing  the IDs of the attribute values to be associated with the product.
     * @param Product $product         An instance of the Product class representing the product whose attribute values are to be adjusted.
     * @return void
     */
    private function adjustProductAttributeValues(array $attributeValues, Product $product): void {
        $product->attributeValues()->sync($attributeValues);
    }

    private function adjustProductVariants($id) {
        // Find the product with it variants, attribute, and attribute values are assigned.
        $oldVariants = Product::with(['productVariants', 'attributes', 'attributeValues'])->findOrFail($id)->productVariants;

        if (empty($oldVariants)) {
            return;
        }

        foreach ($oldVariants as $oldVariant) {
            $oldVariant->delete();
        }
    }

    private function descriptionStore($request, $product) {
        $desingInstruction           = array_filter($request->design_instruction, fn($val) => !is_null($val) && $val !== '');
        $purifier                    = new \HTMLPurifier();
        $product->description        = htmlspecialchars_decode($purifier->purify($request->description));
        $product->short_description  = $request->short_description;
        $product->design_instruction = count($desingInstruction) ? $desingInstruction : NULL;
        $product->save();

        $notify[] = ['success', "Product description saved successfully"];
        if ($request->isUpdateOrNew == 'edit') {
            $notify[] = ['success', "Product description updated successfully"];
        }

        return to_route($this->getRedirectUrl($request->isUpdateOrNew), [$product->slug, Status::STEP_SEO])->withNotify($notify);
    }

    private function seoStore($request, $product) {
        $product->meta_title       = $request->meta_title;
        $product->meta_keywords    = $request->meta_keywords;
        $product->meta_description = $request->meta_description;
        $product->save();

        $notify[] = ['success', "Product seo content saved successfully"];
        if ($request->isUpdateOrNew == 'edit') {
            $notify[] = ['success', "Product seo content updated successfully"];
        }

        return to_route($this->getRedirectUrl($request->isUpdateOrNew), [$product->slug, ($product->product_type == Status::PRODUCT_TYPE_VARIABLE ? Status::STEP_VARIANTS : Status::STEP_MEDIA)])->withNotify($notify);
    }

    private function mediaStore($request, $product) {
        if ($request->hasFile('main_image')) {
            try {
                $product->main_image = fileUploader($request->main_image, getFilePath('product'), getFileSize('product'), $product->main_image, getThumbSize('product'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload image'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('designer_model')) {
            try {
                $product->designer_model = fileUploader(
                    $request->file('designer_model'),
                    base_path('../assets/models/designer'),
                    null,
                    $product->designer_model
                );
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the 3D product model'];
                return back()->withNotify($notify);
            }
        }

        if ($request->has('designer_settings')) {
            $product->designer_settings = Product::normalizeDesignerSettings($request->input('designer_settings', []));
        }

        $product->designer_form = $request->input('designer_form', Product::DESIGNER_FORM_DTG);

        $product->is_published = ($request->published ?? null) ? Status::YES : Status::NO;
        $product->save();

        // upload gallery images
        if ($product->product_type == Status::PRODUCT_TYPE_VARIABLE) {
            $this->uploadVariantImage($request, $product);
        } else {
            // upload general images
            $galleryImages = [];
            foreach ($request->images ?? [] as $image) {
                try {
                    $imageName = fileUploader($image, getFilePath('product'), getFileSize('product'), null, getThumbSize('product'));
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Couldn\'t upload image'];
                    return back()->withNotify($notify);
                }
                $galleryImages[] = [
                    'product_id' => $product->id,
                    'image'      => $imageName,
                    'created_at' => now(),
                ];
            }

            $removeImages = ProductImage::whereNotIn('id', ($request->old_image_id ?? []))->where('product_id', $product->id)->get();
            foreach ($removeImages as $removeImage) {
                $thumbPath = getFilePath('product') . '/thumb_' . $removeImage->image;
                $path      = getFilePath('product') . '/' . $removeImage->image;

                file_exists($thumbPath) ? unlink($thumbPath) : false;
                file_exists($path) ? unlink($path) : false;

                $removeImage->delete();
            }

            if (!blank($galleryImages)) {
                ProductImage::insert($galleryImages);
            }
        }

        $this->ensureDefaultPrintArea($product);

        $notify[] = ['success', $request->isUpdateOrNew === 'new'
            ? 'Product created successfully'
            : 'Product updated successfully'];

        return to_route('vendor.products.index')->withNotify($notify);
    }

    /**
     * Give products working front and back designer areas while preserving all
     * print-area data already configured for existing products.
     */
    private function ensureDefaultPrintArea(Product $product): void {
        $printAreas = $product->productPrintAreas()->get();
        $hasBack = $printAreas->contains(fn($area) => str_contains(strtolower($area->name), 'back'));
        $hasFront = $printAreas->contains(fn($area) => !str_contains(strtolower($area->name), 'back'));

        if (!$hasFront) {
            $this->createDefaultPrintArea($product, 'Front');
        }

        if (!$hasBack) {
            $this->createDefaultPrintArea($product, 'Back');
        }
    }

    private function createDefaultPrintArea(Product $product, string $name): void {
        $printArea                = new ProductPrintArea();
        $printArea->product_id    = $product->id;
        $printArea->name          = $name;
        $printArea->image         = 'default-product-print-area.png';
        $printArea->selected_area = json_encode([
            'type'   => 'rect',
            'left'   => 156,
            'top'    => 90,
            'angle'  => 0,
            'width'  => 285,
            'height' => 440,
        ]);
        $printArea->save();
    }

    public function deleteImage($id) {
        $image = ProductImage::whereHas('product', function ($query) {
            $query->where('vendor_id', authVendorId());
        })->findOrFail($id);

        try {
            $imagePath = getFilePath('product') . '/' . $image->image;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $imageThumbPath = getFilePath('product') . '/thumb_' . $image->image;
            if (file_exists($imageThumbPath)) {
                unlink($imageThumbPath);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        $image->delete();

        $notify[] = ['success', 'Image deleted successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Get the redirect URL after creating or updating a product.
     *
     * @param \App\Models\Product $product
     * @param bool $isUpdateOrNew Indicates whether the product is being updated or new
     * @return string
     */
    private function getRedirectUrl($isUpdateOrNew) {
        if ($isUpdateOrNew == 'new') {
            return 'vendor.products.create';
        } else {
            return 'vendor.products.edit';
        }
    }

    /**
     * Determine if attribute adjustment is needed for a product.
     *
     * This method checks if the product's attributes need to be adjusted based on
     * changes in the product type or if the product is a variable type.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing product data
     * @param \App\Models\Product $product The product being checked
     * @return bool Returns true if attribute adjustment is needed, false otherwise
     */
    private function isAttributeAdjustmentNeeded($request, $product) {
        // When storing new product and product type is simple
        if (!$product->id && $request->product_type == Status::PRODUCT_TYPE_SIMPLE) {
            return false;
        }

        // When storing new product and product type is variable
        if (!$product->id && $request->product_type == Status::PRODUCT_TYPE_VARIABLE) {
            return true;
        }

        if ($product->id && $product->product_type != $request->product_type) {
            return true;
        }

        $oldAttributes = $product->attributeValues->pluck('id')->toArray();
        $newAttributes = array_merge(...array_values($request->attribute_values ?? []));

        if (array_diff($oldAttributes, $newAttributes) || array_diff($newAttributes, $oldAttributes)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generate product variants for a variable product.
     *
     * This method generates all possible combinations of attribute values for a variable product
     * and saves them as product variants.
     *
     * @param int $id Product ID
     * @return \Illuminate\Http\Response
     */
    public function generateVariants($id) {

        $product = Product::with([
            'attributeValues',
            'attributes',
            'productVariants' => function ($variants) {
                $variants->withTrashed();
            },
        ])->where('vendor_id', authVendorid())->findOrFail($id);

        if ($product->product_type != Status::PRODUCT_TYPE_VARIABLE) {
            $notify[] = ['error', 'This product is not a variable product'];
        } else if ($product->attributeValues->count() == 0) {
            $notify[] = ['error', 'This product has no attribute value yet'];
        } else {
            $generatedVariants = $this->generatePossibleVariation($product->attributeValues);

            $this->storeGenerateProductVariants($generatedVariants, $product);
            $notify[] = ['success', 'Product variants generated successfully'];
        }
        return back()->withNotify($notify);
    }

    /**
     * Generate variants according to the attribute_values id
     *
     * @param Collection $attributeValues
     * @return array the array of variants
     */
    public function generatePossibleVariation(Collection $attributeValues) {
        // Group the attribute_values by the attributes
        $attributeGroup = $attributeValues->groupBy('attribute_id');

        $variantsArray = [];

        foreach ($attributeGroup as $attributes) {
            $variantArray = [];
            foreach ($attributes as $attributeValue) {
                $variantArray[] = [
                    'name' => $attributeValue->name,
                    'id'   => $attributeValue->id,
                ];
            }
            $variantsArray[] = $variantArray;
        }

        return $this->generateCombination($variantsArray);
    }

    private function generateCombination($arrays, $currentIndex = 0) {

        $combinations = [];

        // If there's only one array remaining, return its elements as combinations.
        if ($currentIndex === count($arrays) - 1) {
            foreach ($arrays[$currentIndex] as $element) {
                $combinations[] = [$element];
            }
        } else {
            // Recursively generate combinations for the remaining arrays.
            $subCombinations = $this->generateCombination($arrays, $currentIndex + 1);

            foreach ($arrays[$currentIndex] as $element) {
                foreach ($subCombinations as $subCombination) {
                    // Combine the current element with each subCombination.
                    $combinations[] = array_merge([$element], $subCombination);
                }
            }
        }

        return $combinations;
    }

    public function storeGenerateProductVariants($generatedVariants, $product) {
        foreach ($generatedVariants as $variant) {
            $variant        = collect($variant);
            $attributeArray = $this->prepareAttributeValuesArray($variant);
            $savedVariant   = $product->productVariants->where('attribute_values', $attributeArray)->first();

            if ($savedVariant && $savedVariant->trashed()) {
                $savedVariant->restore();
            }

            $productVariant                   = $savedVariant ?? new ProductVariant();
            $productVariant->product_id       = $product->id;
            $productVariant->name             = implode(' - ', $variant->pluck('name')->toArray());
            $productVariant->attribute_values = $attributeArray;

            $productVariant->save();
        }
    }

    /**
     * Prepare the arrays of attribute_values_id from the collection of arrays that contains name, id pair
     *
     * @param Collection $variant the variant by which ids are need to prepare
     * @return array The array of attribute_values
     */
    private function prepareAttributeValuesArray($variant) {
        $attributeValueArray = $variant->pluck('id')->toArray();
        sort($attributeValueArray);
        return $attributeValueArray;
    }

    /**
     * Save generated product variants for a variable product.
     *
     * This method takes an array of generated variants and saves them to the database
     * as product variants associated with the given product.
     *
     * @param array $generatedVariants An array of generated product variants
     * @param \App\Models\Product $product The product to which the variants belong
     * @return void
     */
    private function variantsStore($request, $product) {

        $variants            = $product->productVariants ?? [];
        $minimumRegularPrice = null;
        $minimumSalePrice    = null;

        foreach ($request->variants_id as $key => $variantsId) {
            $variant                = $variants->where('id', $variantsId)->first();
            $variant->regular_price = $request->regular_price[$key] ?? null;
            $variant->sale_price    = $request->sale_price[$key] ?? null;
            $variant->is_published  = ($request->is_published[$key] ?? NULL) ? Status::YES : Status::NO;
            $variant->save();

            if ($variant->is_published) {
                if ($minimumRegularPrice == null && $variant->regular_price) {
                    $minimumRegularPrice = $variant->regular_price;
                } elseif ($minimumRegularPrice > $variant->regular_price) {
                    $minimumRegularPrice = $variant->regular_price;
                }

                if ($minimumSalePrice == null && $variant->sale_price) {
                    $minimumSalePrice = $variant->sale_price;
                } elseif ($minimumSalePrice > $variant->sale_price) {
                    $minimumSalePrice = $variant->sale_price;
                }
            }
        }

        $product->regular_price = $minimumRegularPrice;
        $product->sale_price    = $minimumSalePrice;
        $product->save();

        $notify[] = ['success', "Product variants saved successfully"];
        if ($request->isUpdateOrNew == 'edit') {
            $notify[] = ['success', "Product variants updated successfully"];
        }
        return to_route($this->getRedirectUrl($request->isUpdateOrNew), [$product->slug, Status::STEP_MEDIA])->withNotify($notify);
    }

    private function uploadVariantImage($request, $product) {

        // upload gallery image
        $uploadImages = [];
        foreach ($request->images ?? [] as $variantId => $images) {
            foreach ($images as $image) {
                try {
                    $imageName = fileUploader($image, getFilePath('product'), getFileSize('product'), null, getThumbSize('product'));
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Couldn\'t upload image'];
                    return back()->withNotify($notify);
                }
                $uploadImages[] = [
                    'product_id'         => $product->id,
                    'product_variant_id' => $variantId,
                    'image'              => $imageName,
                    'created_at'         => now(),
                ];
            }
        }

        // delete removed image
        foreach ($request->variant_id as $variantId) {
            $removeImages = ProductImage::whereNotIn('id', ($request->variant_old_image_id[$variantId] ?? []))->where('product_variant_id', $variantId)->where('product_id', $product->id)->get();

            foreach ($removeImages as $removeImage) {
                $thumbPath = getFilePath('product') . '/thumb_' . $removeImage->image;
                $path      = getFilePath('product') . '/' . $removeImage->image;

                file_exists($thumbPath) ? unlink($thumbPath) : false;
                file_exists($path) ? unlink($path) : false;

                $removeImage->delete();
            }
        }

        if (!blank($uploadImages)) {
            ProductImage::insert($uploadImages);
        }

        // upload variation main image
        foreach ($request->variant_id as $key => $variantId) {
            if ($request->variant_main_image[$key] ?? false) {
                try {
                    $variant             = $product->productVariants->where('id', $variantId)->firstOrFail();
                    $variant->main_image = fileUploader($request->variant_main_image[$key], getFilePath('product'), getFileSize('product'), $variant?->main_image, getThumbSize('product'));
                    $variant->save();
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Couldn\'t upload image'];
                    return back()->withNotify($notify);
                }
            }
        }
    }

    public function status($id) {
        $product = Product::where('vendor_id', authVendorId())->findOrFail($id);

        if (!$product->is_published) {
            $this->ensureDefaultPrintArea($product);
        }

        $product->is_published = $product->is_published ? Status::NO : Status::YES;
        $product->save();

        $notify[] = ['success', 'Published status changed successfully'];
        return back()->withNotify($notify);
    }

    public function reviews() {
        $pageTitle = "Reviews";
        $reviews   = ProductReview::searchable(['product:name', 'rating', 'order:order_number'])
            ->whereHas('product', function ($query) {
                $query->where('vendor_id', authVendorId());
            })->orderBy('id', 'desc')->paginate(getPaginate());

        return view('Template::vendor.product.reviews', compact('pageTitle', 'reviews'));
    }
}
