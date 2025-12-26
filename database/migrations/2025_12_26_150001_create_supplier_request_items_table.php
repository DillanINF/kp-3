<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_request_id')->constrained('supplier_requests')->cascadeOnDelete();
            $table->string('product_name');
            $table->string('unit', 30)->default('pcs');
            $table->unsignedInteger('qty');
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_request_items');
    }
};
