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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_code', 32)->nullable()->unique()->after('id');
            $table->string('payment_method', 20)->default('QRIS')->after('total_price');
            $table->string('payment_status', 20)->default('UNPAID')->after('payment_method'); // UNPAID, PAID, REFUNDED
            $table->string('payment_reference', 64)->nullable()->after('payment_status');
            $table->text('qris_payload')->nullable()->after('payment_reference');
            $table->dateTime('payment_expired_at')->nullable()->after('qris_payload');
            $table->dateTime('paid_at')->nullable()->after('payment_expired_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'booking_code',
                'payment_method',
                'payment_status',
                'payment_reference',
                'qris_payload',
                'payment_expired_at',
                'paid_at',
            ]);
        });
    }
};
