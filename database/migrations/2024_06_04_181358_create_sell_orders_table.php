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
        Schema::create('sell_orders', function (Blueprint $table) {
            $table->id('sell_order_id'); // Auto-incrementing primary key
            $table->unsignedBigInteger('customer_id'); // Foreign key column
            $table->unsignedBigInteger('finish_product_id'); // Foreign key column
            $table->integer('quantity'); // INT NOT NULL column
            $table->decimal('unit_price', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->decimal('total_amount', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->decimal('grand_total', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->decimal('paid_amount', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->decimal('remaining_amount', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->foreign('customer_id')->references('customer_id')->on('customer'); // Foreign key constraint
            $table->foreign('finish_product_id')->references('finish_product_id')->on('finish_product'); // Foreign key constraint
            $table->timestamps(); // Created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sell_orders');
    }
};
