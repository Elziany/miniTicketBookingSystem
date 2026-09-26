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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->enum('status', [
                'unpublished',
                'under_reservations',
                'out_of_seats',
                'ready_to_start',
                'started',
                'ended',
                'cancelled'
            ])->default('unpublished');
            $table->enum('approval_mode', ['auto', 'manual'])->default('auto');
            $table->integer('approval_window_hours')->nullable();
            $table->enum('pricing_mode', ['per_seat', 'per_category']);
            $table->json('custom_refund_policy')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
