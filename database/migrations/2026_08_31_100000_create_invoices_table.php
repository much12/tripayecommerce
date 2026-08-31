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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();                                             // Auto-increment, Primary Key
            $table->foreignId('product_id')->constrained()->cascadeOnDelete(); // FK ke products
            $table->string('tripay_reference')->nullable();           // Reference dari respons API TriPay
            $table->string('buyer_email');                            // Wajib diisi
            $table->string('buyer_phone');                            // Wajib diisi
            $table->json('raw_response')->nullable();                 // Seluruh respons JSON dari API TriPay

            // Kolom pendukung alur pembayaran
            $table->string('merchant_ref')->unique();                 // Nomor invoice sistem kita
            $table->unsignedInteger('amount');                        // Nominal pembayaran
            $table->string('payment_method')->nullable();             // Kode channel (mis. BRIVA, QRIS)
            $table->string('checkout_url')->nullable();               // URL halaman bayar TriPay
            $table->string('status')->default('UNPAID');              // UNPAID, PAID, EXPIRED, FAILED, REFUND
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
