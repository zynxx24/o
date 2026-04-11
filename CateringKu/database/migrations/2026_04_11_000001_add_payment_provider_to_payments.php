<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only add column if it doesn't already exist
        if (!Schema::hasColumn('payments', 'payment_provider')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('payment_provider', 50)->nullable()->after('payment_method');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('payments', 'payment_provider')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('payment_provider');
            });
        }
    }
};
