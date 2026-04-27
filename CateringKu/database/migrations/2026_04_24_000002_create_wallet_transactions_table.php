<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wallet_id');
            $table->enum('type', ['credit', 'debit']);
            $table->enum('category', [
                'commission',       // vendor menerima payout order
                'platform_fee',     // platform menerima fee
                'tax',              // pajak negara
                'withdrawal',       // vendor tarik dana
                'deposit',          // deposit awal vendor
                'deposit_refund',   // deposit dikembalikan saat akun ditutup
                'adjustment',       // koreksi manual admin
            ]);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);       // snapshot saldo setelah transaksi
            $table->decimal('frozen_after', 15, 2)->default(0); // snapshot frozen setelah transaksi
            $table->string('reference_type')->nullable();  // 'order', 'withdrawal_request', 'vendor_application'
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description');
            $table->timestamps();

            $table->foreign('wallet_id')->references('wallet_id')->on('wallets')->cascadeOnDelete();
            $table->index(['wallet_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
