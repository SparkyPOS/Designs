<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class ProductVariant extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $casts = [
        'attribute_values' => 'array',
        'sale_price'         => 'double',
        'regular_price'      => 'double',
        'sale_starts_from'   => 'datetime',
        'sale_ends_at'       => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function galleryImages($id = null)
    {
        $images = $this->hasMany(ProductImage::class);
        if($id) {
            $images->where('product_variation_id', $id);
        }
        return $images;
    }

    public function mainImage($thumb = true)
    {
        $image = $this->displayImage;
        $thumb = $thumb ? '/thumb_' : '/';

        if (!$image) {
            return getImage(null);
        }
        return getImage($image->path . $thumb . $image->file_name);
    }

    public function scopePublished($query)
    {
        return $query->where('product_variants.is_published', Status::YES);
    }

}
