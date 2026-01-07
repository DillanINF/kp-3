<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->unique('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_approvals');
    }
};
