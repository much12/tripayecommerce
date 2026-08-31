<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();                                 // Auto-increment, Primary Key
            $table->string('sku')->unique();              // Kode unik produk
            $table->string('name');                       // Nama produk
            $table->unsignedInteger('price');             // Harga produk
            $table->string('reference')->nullable();      // Merchant Reference untuk TriPay
            $table->timestamps();                         // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
