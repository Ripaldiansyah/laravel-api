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
        Schema::create('tr_h_supplier_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('tr_h_purchase')->constrained();
            $table->string('total_quantity');
            $table->string('total_amount');
            $table->string('reason')->nullable();
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('tr_d_supplier_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tr_h_supplier_returns')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->decimal('return_price');
            $table->integer('return_quantity');
            $table->decimal('return_amount');
            $table->integer('return_quantity_approve')->nullable();
            $table->decimal('return_amount_approve')->nullable();
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
