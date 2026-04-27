<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->unique();
            $table->decimal('gross_amount', 15, 2);    // total_amount order
            $table->decimal('tax_amount', 15, 2);      // pajak negara
            $table->decimal('platform_amount', 15, 2); // fee platform
            $table->decimal('vendor_amount', 15, 2);   // payout vendor
            $table->decimal('tax_rate', 5, 2);
            $table->decimal('platform_rate', 5, 2);
            $table->enum('status', ['pending', 'distributed'])->default('pending');
            $table->timestamp('distributed_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_commissions');
    }
};
