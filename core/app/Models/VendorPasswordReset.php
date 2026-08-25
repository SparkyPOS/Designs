<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorPasswordReset extends Model
{
    protected $table = "vendor_password_resets";
    protected $guarded = ['id'];
    public $timestamps = false;
}
