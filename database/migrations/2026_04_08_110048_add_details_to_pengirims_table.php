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
        Schema::table('pengirims', function (Blueprint $table) {
            $table->string('phone', 50)->nullable()->after('name');
            $table->string('vehicle_type', 100)->nullable()->after('phone');
            $table->string('license_plate', 20)->nullable()->after('vehicle_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengirims', function (Blueprint $table) {
            $table->dropColumn(['phone', 'vehicle_type', 'license_plate']);
        });
    }
};
