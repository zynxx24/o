<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id('item_id');
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('item_name', 100);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('min_order')->default(1);
            $table->string('unit', 20)->default('porsi');
            $table->string('image_url', 255)->nullable();
            $table->boolean('is_available')->default(true);
            $table->integer('preparation_time')->nullable()->comment('dalam menit');
            $table->enum('spicy_level', ['tidak pedas', 'sedang', 'pedas', 'sangat pedas'])->default('tidak pedas');
            $table->timestamps();
            $table->foreign('vendor_id')->references('vendor_id')->on('vendors')->cascadeOnDelete();
            $table->foreign('category_id')->references('category_id')->on('categories')->nullOnDelete();
            $table->index('vendor_id');
            $table->index('category_id');
            $table->index('price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
