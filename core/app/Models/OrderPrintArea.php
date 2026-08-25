<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPrintArea extends Model
{
    public function productPrintArea()
    {
        return $this->belongsTo(ProductPrintArea::class);
    }
}
