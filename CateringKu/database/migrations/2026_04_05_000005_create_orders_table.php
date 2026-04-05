<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('vendor_id');
            $table->string('order_number', 50)->unique();
            $table->enum('order_type', ['package', 'custom']);
            $table->string('event_type', 50)->nullable();
            $table->date('event_date');
            $table->time('event_time');
            $table->text('delivery_address');
            $table->string('delivery_city', 50)->nullable();
            $table->integer('num_people');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax', 12, 2)->default(0.00);
            $table->decimal('delivery_fee', 10, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'delivering', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid');
            $table->text('special_request')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->foreign('vendor_id')->references('vendor_id')->on('vendors')->cascadeOnDelete();
            $table->index('vendor_id');
            $table->index('status');
            $table->index('event_date');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id('order_item_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('package_id')->nullable();
            $table->string('item_name', 100);
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->text('notes')->nullable();
            $table->foreign('order_id')->references('order_id')->on('orders')->cascadeOnDelete();
            $table->foreign('item_id')->references('item_id')->on('menu_items')->nullOnDelete();
            $table->foreign('package_id')->references('package_id')->on('packages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
