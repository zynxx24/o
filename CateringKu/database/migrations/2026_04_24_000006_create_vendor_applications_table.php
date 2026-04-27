<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('vendor_name');
            $table->text('description')->nullable();
            $table->text('address');
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->decimal('deposit_amount', 15, 2)->default(10000000); // Rp 10 juta
            $table->string('payment_proof')->nullable();   // URL bukti transfer
            $table->string('payment_method')->nullable();  // bank transfer, etc
            $table->enum('status', [
                'draft',             // belum submit
                'submitted',         // sudah submit, menunggu review
                'deposit_pending',   // profil ok, menunggu bukti deposit
                'approved',          // disetujui, akun vendor aktif
                'rejected',          // ditolak
            ])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
            // Satu user hanya bisa punya 1 aplikasi aktif (non-rejected)
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_applications');
    }
};
