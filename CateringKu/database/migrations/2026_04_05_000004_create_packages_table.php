<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id('package_id');
            $table->unsignedBigInteger('vendor_id');
            $table->string('package_name', 100);
            $table->text('description')->nullable();
            $table->decimal('price_per_person', 10, 2);
            $table->integer('min_person')->default(10);
            $table->integer('max_person')->nullable();
            $table->enum('package_type', ['prasmanan', 'nasi kotak', 'snack box', 'custom']);
            $table->string('image_url', 255)->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->foreign('vendor_id')->references('vendor_id')->on('vendors')->cascadeOnDelete();
            $table->index('vendor_id');
            $table->index('package_type');
        });

        Schema::create('package_items', function (Blueprint $table) {
            $table->id('package_item_id');
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('item_id');
            $table->integer('quantity')->default(1);
            $table->foreign('package_id')->references('package_id')->on('packages')->cascadeOnDelete();
            $table->foreign('item_id')->references('item_id')->on('menu_items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_items');
        Schema::dropIfExists('packages');
    }
};
