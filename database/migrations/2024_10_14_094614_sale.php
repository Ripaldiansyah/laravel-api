<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tr_h_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->decimal('total_amount', 10, 2);
            $table->integer('total_quantity');
            $table->timestamp('sold_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('tr_d_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tr_h_sales')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->string('product_name');
            $table->string('category_name');
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total', 10, 2);
            $table->timestamp('sold_at')->useCurrent();
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
