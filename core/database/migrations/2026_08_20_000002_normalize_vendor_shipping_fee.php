<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('vendors')->whereNull('shipping_fee')->update(['shipping_fee' => 0]);
        DB::statement('ALTER TABLE vendors MODIFY shipping_fee DECIMAL(28,8) NOT NULL DEFAULT 0.00000000');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE vendors MODIFY shipping_fee DECIMAL(28,8) NULL DEFAULT NULL');
    }
};
