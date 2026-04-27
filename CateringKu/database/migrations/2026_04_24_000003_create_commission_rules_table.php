<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('default');
            $table->decimal('tax_rate', 5, 2)->default(11.00);       // PPN %
            $table->decimal('platform_rate', 5, 2)->default(10.00);  // Platform fee % dari net after tax
            // vendor_rate = 100 - platform_rate (auto-calculated)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_rules');
    }
};
