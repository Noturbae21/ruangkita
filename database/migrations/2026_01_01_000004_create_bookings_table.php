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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('booker_name');
            $table->string('booker_contact');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->unsignedInteger('participants');
            $table->unsignedBigInteger('total_price');
            $table->string('status', 20)->default('CONFIRMED');
            $table->dateTime('cancelled_at')->nullable();
            $table->unsignedBigInteger('penalty_amount')->default(0);
            $table->timestamps();

            $table->index(['room_id', 'start_time', 'end_time'], 'bookings_room_time_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
