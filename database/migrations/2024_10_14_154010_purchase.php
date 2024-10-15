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
        Schema::create('tr_h_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('supplier_id')->constrained();
            $table->string('total_quantity');
            $table->string('total_amount');
            $table->string('status');
            $table->timestamps();
        });
        Schema::create('tr_d_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tr_h_purchase')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->decimal('purchase_price');
            $table->integer('purchase_quantity');
            $table->decimal('purchase_amount');
            $table->integer('purchase_quantity_release')->nullable();
            $table->decimal('purchase_amount_release')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
