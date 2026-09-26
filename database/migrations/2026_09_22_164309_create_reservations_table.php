<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seat_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reservation_reference')->unique();
            $table->decimal('price_paid', 10, 2);
            $table->enum('status', ['held', 'pending_approval', 'confirmed', 'rejected', 'expired', 'cancelled']);
            $table->string('qr_code_path')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->decimal('refunded_amount', 10, 2)->default(0.00);
            $table->text('cancellation_reason')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
