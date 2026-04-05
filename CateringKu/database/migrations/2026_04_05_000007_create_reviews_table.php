<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('review_id');
            $table->unsignedBigInteger('order_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('vendor_id');
            $table->tinyInteger('rating');
            $table->tinyInteger('food_rating')->nullable();
            $table->tinyInteger('service_rating')->nullable();
            $table->tinyInteger('delivery_rating')->nullable();
            $table->text('review_text')->nullable();
            $table->text('images')->nullable();
            $table->text('vendor_response')->nullable();
            $table->timestamp('response_date')->nullable();
            $table->timestamps();
            $table->foreign('order_id')->references('order_id')->on('orders')->cascadeOnDelete();
            $table->foreign('vendor_id')->references('vendor_id')->on('vendors')->cascadeOnDelete();
            $table->index('vendor_id');
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
