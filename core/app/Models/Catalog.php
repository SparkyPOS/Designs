<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Catalog extends Model {
    use GlobalStatus;

    protected $casts = [
        'meta_keywords'      => 'array'
    ];

    public function catalogCategory() {
        return $this->belongsTo(CatalogCategory::class);
    }

    public function product() {
        return $this->hasMany(Product::class);
    }

    public function getSeoImageAttribute() {
        return getImage(getFilePath('catalog') . '/' . $this->image);
    }

    public function getSeoImageSizeAttribute() {
        return getFileSize('catalog');
    }
}
