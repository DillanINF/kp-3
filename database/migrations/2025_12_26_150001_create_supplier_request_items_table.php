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
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('unit', 30)->default('pcs');
            $table->unsignedInteger('qty');
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->timestamps();

            $table->index(['supplier_request_id', 'item_id']);
        });

        Schema::create('supplier_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->unsignedBigInteger('buy_price')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['supplier_id', 'item_id']);
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->date('date');
            $table->string('po_no')->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('grand_total')->default(0);
            $table->unsignedInteger('qty_total')->default(0);
            $table->string('status', 20)->default('draft')->index();
            $table->timestamps();
        });

        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->unsignedInteger('qty');
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->timestamps();

            $table->index(['invoice_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_details');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('supplier_items');
        Schema::dropIfExists('supplier_request_items');
    }
};
