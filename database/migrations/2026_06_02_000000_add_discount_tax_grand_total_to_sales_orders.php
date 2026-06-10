<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->decimal('discount',    15, 2)->default(0)->after('total_amount');
            $table->decimal('tax',         15, 2)->default(0)->after('discount');
            $table->decimal('grand_total', 15, 2)->default(0)->after('tax');
        });

        DB::statement('UPDATE sales_orders SET grand_total = total_amount WHERE grand_total = 0');
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn(['discount', 'tax', 'grand_total']);
        });
    }
};
