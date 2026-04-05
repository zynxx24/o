<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id('vendor_id');
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('vendor_name', 100);
            $table->text('description')->nullable();
            $table->text('address');
            $table->string('city', 50)->nullable();
            $table->string('province', 50)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('logo_url', 255)->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
            $table->timestamps();
            $table->index('city');
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
