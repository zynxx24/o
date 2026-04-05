<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart', function (Blueprint $table) {
            $table->id('cart_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('vendor_id');
            $table->timestamps();
            $table->foreign('vendor_id')->references('vendor_id')->on('vendors')->cascadeOnDelete();
            $table->unique(['user_id', 'vendor_id']);
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id('cart_item_id');
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('package_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->text('notes')->nullable();
            $table->timestamp('added_at')->useCurrent();
            $table->foreign('cart_id')->references('cart_id')->on('cart')->cascadeOnDelete();
            $table->foreign('item_id')->references('item_id')->on('menu_items')->cascadeOnDelete();
            $table->foreign('package_id')->references('package_id')->on('packages')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('cart');
    }
};
