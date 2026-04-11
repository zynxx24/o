<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('order_id');
            $table->string('payment_method', 50);
            $table->decimal('amount', 12, 2);
            $table->timestamp('payment_date')->useCurrent();
            $table->string('payment_proof', 255)->nullable();
            $table->enum('payment_status', ['pending', 'verified', 'failed', 'refunded'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreign('order_id')->references('order_id')->on('orders')->cascadeOnDelete();
            $table->index('order_id');
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
