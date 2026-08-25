<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CatalogCategory extends Model
{
    use GlobalStatus;

    protected $casts = [
        'meta_keywords'      => 'array'
    ];

    public function catalogs()
    {
        return $this->hasMany(Catalog::class);
    }

    public function scopeGeneral($query) {
        return $query->where('feature', Status::GENERAL);
    }

    public function scopeFeatured($query) {
        return $query->where('feature', Status::FEATURED);
    }

    public function scopeMegaMenu($query) {
        return $query->where('feature', Status::MEGA_MENU);
    }

    public function featureBadge(): Attribute
    {
        return new Attribute(
            get: fn () => $this->featureBadgeData(),
        );
    }

    public function featureBadgeData()
    {
        $html = '';
        if ($this->feature == Status::FEATURED) {
            $html = '<span class="badge badge--primary">' . trans('Featured') . '</span>';
        } elseif ($this->feature == Status::MEGA_MENU) {
            $html = '<span class="badge badge--success">' . trans('Mega Menu') . '</span>';
        } else {
            $html = '<span class="badge badge--info">' . trans('General') . '</span>';
        }
        return $html;
    }

    public function getSeoImageAttribute() {
        return getImage(getFilePath('catalogCategory') . '/' . $this->image);
    }

    public function getSeoImageSizeAttribute() {
        return getFileSize('catalogCategory');
    }
}
