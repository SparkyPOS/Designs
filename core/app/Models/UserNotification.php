<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    public function vendor()
    {
    	return $this->belongsTo(Vendor::class);
    }
}
