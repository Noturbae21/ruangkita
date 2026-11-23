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
        Schema::table('rooms', function (Blueprint $table) {
            $table->decimal('length_m', 5, 1)->default(6.0)->after('capacity');
            $table->decimal('width_m', 5, 1)->default(4.0)->after('length_m');
            $table->string('layout_type', 30)->default('BOARDROOM')->after('width_m'); // BOARDROOM, U_SHAPE, CLASSROOM, POD
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['length_m', 'width_m', 'layout_type']);
        });
    }
};
