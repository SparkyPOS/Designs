<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Vendor;
use App\Models\Catalog;
use App\Models\Product;
use App\Models\Frontend;
use App\Constants\Status;
use App\Models\ProductReview;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use App\Models\CatalogCategory;

class ProductController extends Controller {
    public function allCatalogCategory() {
        $pageTitle      = 'All Catalogs';
        $categories       = CatalogCategory::active()->searchable(['name'])->orderBy('name')->paginate(16);
        $catalogContent = Frontend::where('tempname', activeTemplateName())->where('data_keys', 'catalog.content')->first()->data_values ?? NULL;
        return view('Template::category', compact('pageTitle', 'categories', 'catalogContent'));
    }

    public function catalogs($categorySlug) {
        $category       = CatalogCategory::where('slug', $categorySlug)->active()->firstOrFail();
        $pageTitle      = $category->name;
        $catalogs       = $category->catalogs()->active()->searchable(['name'])->orderBy('name')->paginate(getPaginate());
        $catalogContent = Frontend::where('tempname', activeTemplateName())->where('data_keys', 'catalog.content')->first()->data_values ?? NULL;
        $seoContents        = getSeoContents($category);
        $seoImage           = $seoContents->image ?? NULL;
        return view('Template::catalogs', compact('pageTitle', 'catalogs', 'category', 'catalogContent', 'seoContents', 'seoImage'));
    }

    public function products($categorySlug, $catalogSlug) {
        $catalog = Catalog::active()
            ->where('slug', $catalogSlug)
            ->whereHas('catalogCategory', function ($query) use ($categorySlug) {
                $query->where('slug', $categorySlug)->active();
            })
            ->firstOrFail();
        $pageTitle = $catalog->name;

        $productQurery  = Product::with('vendor')
            ->where('catalog_id', $catalog->id)
            ->searchable(['name'])
            ->published();

        if (request()->sort_by) {
            $productQurery->orderBy('sale_price', request()->sort_by);
        }

        $products = $productQurery->paginate(getPaginate());
        $productContent = Frontend::where('tempname', activeTemplateName())->where('data_keys', 'product.content')->first()->data_values ?? NULL;
        if (request()->ajax()) {
            $html = view('Template::partials.product', compact('products'))->render();
            return responseSuccess("product_filter", "Success", ['html' => $html]);
        }
        $seoContents        = getSeoContents($catalog);
        $seoImage           = $seoContents->image ?? NULL;
        return view('Template::products', compact('pageTitle', 'catalog', 'products', 'productContent', 'seoContents', 'seoImage'));
    }

    public function vendorProducts($username) {
        $vendor   = Vendor::active()->where('username', $username)->firstOrFail();
        $pageTitle = $vendor->name . ' all products';
        $productQurery  = Product::with('vendor')
            ->where('vendor_id', $vendor->id)
            ->searchable(['name'])
            ->published();
        if (request()->sort_by) {
            $productQurery->orderBy('sale_price', request()->sort_by);
        }
        $products = $productQurery->paginate(getPaginate());
        if (request()->ajax()) {
            $html = view('Template::partials.product', compact('products'))->render();
            return responseSuccess("product_filter", "Success", ['html' => $html]);
        }
        return view('Template::vendor_products', compact('pageTitle', 'vendor', 'products'));
    }

    public function productDetails($slug) {
        $product = Product::query();

        if (!auth()->guard('vendor')->user()) {
            $product->published();
        }

        $product = $product->with($this->productDetailsRelations())
            ->withCount(['reviews' => function ($rating) {
                $rating->where('status', 1);
            }])
            ->withAvg(['reviews' => function ($q2) {
                $q2->where('status', 1);
            }], 'rating')
            ->where('slug', $slug)
            ->firstOrFail();

        $images = $product->galleryImages;

        $data = compact('product', 'images');

        list($otherProductsTitle, $otherProducts) = $this->getOtherProducts($product);

        $data['pageTitle']          = 'Product Details - ' . $product->name;
        $data['otherProductsTitle'] = $otherProductsTitle;
        $data['otherProducts']      = $otherProducts;
        $data['seoContents']        = getSeoContents($product);
        $data['seoImage']           = $data['seoContents']->image ?? NULL;
        return view('Template::product_details', $data);
    }

    private function productDetailsRelations() {
        return [
            'catalog:id,catalog_category_id,name,slug',
            'productVariants' => function ($productVariant) {
                return $productVariant->published();
            },
            'attributes:id,name,type',
            'attributeValues:id,attribute_id,name,value',
            'galleryImages',
            'productPrintAreas',
            'vendor',
            'reviews.user'
        ];
    }

    private function getOtherProducts($product, $limit = 20, $recentDays = 30) {
        $title = 'Related Products';
        $query = Product::where('id', '!=', $product->id)->published()->where('catalog_id', $product->catalog_id);

        $products = $query->with([
            'productVariants' => fn($q) => $q->published(),
        ])->withCount(['reviews' => function ($review) {
            $review->where('status', Status::REVIEW_APPROVED);
        }])->with('vendor')->take($limit)->get();

        if ($products->isEmpty()) {
            $title = 'Latest Products';

            $products = Product::where('id', '!=', $product->id)
                ->published()
                ->where('created_at', '>=', now()->subDays($recentDays))
                ->latest()
                ->take($limit)
                ->with('vendor')
                ->get();
        }

        return [$title, $products];
    }

    public function productDesign($slug, $variantId = null) {
        $pageTitle      = "Product Design";
        $product        = Product::with('attributes')->where('slug', $slug)->published()->firstOrFail();
        $printAreas     = $product->productPrintAreas;
        $productVariant = null;
        $colorCode = null;
        if ($variantId) {
            $productVariant = ProductVariant::where('product_id', $product->id)->published()->findOrFail($variantId);

            $colorValue = AttributeValue::whereIn('id', $productVariant->attribute_values)
                ->whereHas('attribute', function ($query) {
                    $query->where('type', Status::ATTRIBUTE_TYPE_COLOR);
                })
                ->first();

            $colorCode = $colorValue ? $colorValue->value : NULL;
        }

        $queryProduct = Cart::with('cartPrintAreas')->where('product_id', $product->id);

        if (auth()->check()) {
            $queryProduct->where('user_id', auth()->id());
        } else {
            $queryProduct->where('session_id', getSessionId());
        }
        if ($variantId) {
            $queryProduct->where('product_variant_id', $variantId);
        }
        $cartProduct     = $queryProduct->first();
        $cartPrintAreas = $cartProduct ? $cartProduct->cartPrintAreas : [];
        return view('Template::product_design', compact('product', 'productVariant', 'pageTitle', 'printAreas', 'cartPrintAreas', 'cartProduct', 'colorCode'));
    }

    public function cartProductDesign($id) {
        $queryProduct = Cart::query();
        if (auth()->check()) {
            $queryProduct->where('user_id', auth()->id());
        } else {
            $queryProduct->where('session_id', getSessionId());
        }

        $cartProduct =  $queryProduct->with('product.productPrintAreas', 'productVariant', 'cartPrintAreas')->findOrFail($id);
        $pageTitle   = "Product Design";

        $product = $cartProduct->product;
        $productVariant = $cartProduct->productVariant;
        $printAreas = $product->productPrintAreas;
        $cartPrintAreas = $cartProduct ? $cartProduct->cartPrintAreas : [];
        $colorCode = null;
        if ($productVariant) {
            $colorValue = AttributeValue::whereIn('id', $productVariant->attribute_values)
                ->whereHas('attribute', function ($query) {
                    $query->where('type', Status::ATTRIBUTE_TYPE_COLOR);
                })
                ->first();

            $colorCode = $colorValue ? $colorValue->value : NULL;
        }

        return view('Template::product_design', compact('product', 'productVariant', 'pageTitle', 'printAreas', 'cartPrintAreas', 'cartProduct', 'colorCode'));
    }

    public function productReviews($slug) {
        $pageTitle = "Product Reviews";
        $product        = Product::where('slug', $slug)->published()->firstOrFail();
        $reviewQuery = ProductReview::where('product_id', $product->id);
        if (request()->sort_by) {
            $reviewQuery->orderBy('id', request()->sort_by);
        }
        $reviews = $reviewQuery->paginate(getPaginate());

        if (request()->ajax()) {
            $html = view('Template::partials.reviews', compact('reviews'))->render();
            return responseSuccess("review_filters", "Success", ['html' => $html]);
        }
        return view('Template::product_reviews', compact('pageTitle', 'product', 'reviews'));
    }
}
