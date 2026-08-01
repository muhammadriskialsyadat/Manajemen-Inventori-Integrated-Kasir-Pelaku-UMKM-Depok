<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Tambah kolom supplier_id sebagai default supplier produk.
            // Nullable karena:
            //   1. Data produk lama tidak punya supplier default
            //   2. Produk bisa saja belum ditentukan supplier utamanya
            // SET NULL karena jika supplier dihapus,
            //   produk tidak boleh ikut terhapus — cukup supplier_id-nya di-null-kan.
            $table->foreignId('supplier_id')
                  ->nullable()
                  ->after('category_id')
                  ->constrained('suppliers')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });
    }
};
