<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute as CastAttribute;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    use GlobalStatus;

    public const DESIGNER_FORM_DTG = 'dtg';
    public const DESIGNER_FORM_ENGRAVE = 'engrave';

    protected $casts = [
        'sale_price'         => 'double',
        'regular_price'      => 'double',
        'reviews_avg_rating' => 'double',
        'meta_keywords'      => 'array',
        'design_instruction'      => 'array',
        'short_description'      => 'array',
        'designer_settings'      => 'array',
    ];

    public function catalog() {
        return $this->belongsTo(Catalog::class);
    }

    public function orders() {
        return $this->hasMany(Order::class)
            ->where('vendor_id');
    }

    public function orderDetails() {
        return $this->hasMany(OrderDetail::class);
    }

    public function attributes() {
        return $this->belongsToMany(Attribute::class);
    }

    public function attributeValues() {
        return $this->belongsToMany(AttributeValue::class);
    }

    public function productVariants() {
        return $this->hasMany(ProductVariant::class);
    }

    public function galleryImages() {
        return $this->hasMany(ProductImage::class);
    }

    public function productPrintAreas() {
        return $this->hasMany(ProductPrintArea::class);
    }

    public function vendor() {
        return $this->belongsTo(Vendor::class);
    }

    public function reviews() {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    public function reviewCountsByStars($star = null) {
        $counts = $this->reviews()
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating')
            ->toArray();

        $totalReviews = array_sum($counts);

        $calcPercentage = fn($count) => $totalReviews > 0 ? round(($count / $totalReviews) * 100, 2) : 0;

        if ($star !== null) {
            $count      = $counts[$star] ?? 0;
            $percentage = $calcPercentage($count);

            return [
                'count'      => $count,
                'percentage' => $percentage,
            ];
        }

        $result = [];
        foreach ($counts as $rating => $count) {
            $result[$rating] = [
                'count'      => $count,
                'percentage' => $calcPercentage($count),
            ];
        }

        for ($i = 1; $i <= 5; $i++) {
            if (!isset($result[$i])) {
                $result[$i] = ['count' => 0, 'percentage' => 0];
            }
        }

        ksort($result);
        return $result;
    }



    public function averageRating() {
        return averageRating($this->reviews->sum('rating') ?? 0, $this->reviews->count() ?? 0);
    }

    public function showRating() {
        return showRating($this->averageRating());
    }

    /*======= Scopes =======*/
    public function scopeUnpublished($query) {
        return $query->where('is_published', Status::NO);
    }

    public function scopePublished($query) {
        return $query->where('is_published', Status::YES);
    }

    /*======= Helper Methods =======*/

    public function link() {
        return "";
    }

    public function mainImage($thumb = false) {
        $thumb = $thumb ? '/thumb_' : '/';
        return getImage(getFilePath('product') . $thumb . $this->main_image);
    }

    public function usesDesigner(): bool {
        // Legacy products created before the opt-in flag keep using the designer.
        return $this->use_designer === null || (bool) $this->use_designer;
    }

    public function designerModelUrl() {
        $filename = $this->designer_model ?: '02.glb';
        return asset('assets/models/designer/' . $filename);
    }

    public function designerPreviewUrl() {
        $filename = $this->designer_preview ?: '2.webp';
        return asset('assets/images/designer/' . $filename);
    }

    public function resolvedDesignerForm(): string {
        return in_array($this->designer_form, [self::DESIGNER_FORM_DTG, self::DESIGNER_FORM_ENGRAVE], true)
            ? $this->designer_form
            : self::DESIGNER_FORM_DTG;
    }

    public function usesEngraveDesigner(): bool {
        return $this->resolvedDesignerForm() === self::DESIGNER_FORM_ENGRAVE;
    }

    public static function designerSettingsDefaults(): array {
        return [
            'model_scale'  => 1,
            'rotation_x'   => 0,
            'rotation_y'   => 0,
            'rotation_z'   => 0,
            'offset_x'     => 0,
            'offset_y'     => 0,
            'front_x'      => 0,
            'front_y'      => 0,
            'front_width'  => 1,
            'front_height' => 1,
            'back_x'       => 0,
            'back_y'       => 0,
            'back_width'   => 1,
            'back_height'  => 1,
        ];
    }

    public static function normalizeDesignerSettings(array $settings): array {
        return collect(self::designerSettingsDefaults())
            ->mapWithKeys(fn($default, $key) => [$key => (float) ($settings[$key] ?? $default)])
            ->all();
    }

    public function resolvedDesignerSettings(): array {
        return self::normalizeDesignerSettings($this->designer_settings ?? []);
    }

    public function editUrl() {
        return route("vendor.products.edit", $this->id);
    }

    public function getSeoImageAttribute() {
        return $this->mainImage(false);
    }

    public function getSeoImageSizeAttribute() {
        return getFileSize('product');
    }

    public function publishBadge(): CastAttribute {
        return new CastAttribute(
            get: fn() => $this->pbulishBadgeData(),
        );
    }

    public function pbulishBadgeData() {
        $html = '';
        if ($this->is_published == Status::ENABLE) {
            $html = '<span class="badge custom--badge badge--success">' . trans('Published') . '</span>';
        } else {
            $html = '<span class="badge custom--badge badge--warning">' . trans('Unpublished') . '</span>';
        }
        return $html;
    }

    public function getSalePrice($variationId = false) {
        if ($variationId) {
            return $this->productVariants()->find($variationId)->sale_price ?? 0;
        }
        return $this->sale_price ?? 0;
    }

    public function getRegularPrice($variationId = false) {
        if ($variationId) {
            return $this->productVariants()->find($variationId)->regular_price ?? 0;
        }
        return $this->regular_price ?? 0;
    }

    public function formattedPrice() {
        if ($this->product_type == Status::PRODUCT_TYPE_VARIABLE) {
            $minSalePrice = $this->productVariants->min('sale_price');
            $maxSalePrice = $this->productVariants->max('sale_price');
            return showAmount($minSalePrice) . ' - ' . showAmount($maxSalePrice);
        }
        return showAmount($this->sale_price);
    }
}
