<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah enum reference_type untuk menambahkan 'sale_rollback'
        DB::statement("ALTER TABLE stock_movements MODIFY COLUMN reference_type ENUM('purchase', 'sale', 'adjustment', 'sale_rollback') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum lama
        DB::statement("ALTER TABLE stock_movements MODIFY COLUMN reference_type ENUM('purchase', 'sale', 'adjustment') NOT NULL");
    }
};
