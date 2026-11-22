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
        Schema::create('additional_facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('price')->default(0);
            $table->string('pricing_type', 30)->default('PER_BOOKING'); // PER_BOOKING, PER_HOUR, PER_PARTICIPANT
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('booking_additional_facility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('additional_facility_id')->nullable()->constrained('additional_facilities')->nullOnDelete();
            $table->string('facility_name');
            $table->string('pricing_type', 30)->default('PER_BOOKING');
            $table->unsignedBigInteger('unit_price')->default(0);
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->timestamps();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('room_price')->nullable()->after('participants');
            $table->unsignedBigInteger('additional_facilities_price')->default(0)->after('room_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['room_price', 'additional_facilities_price']);
        });

        Schema::dropIfExists('booking_additional_facility');
        Schema::dropIfExists('additional_facilities');
    }
};
