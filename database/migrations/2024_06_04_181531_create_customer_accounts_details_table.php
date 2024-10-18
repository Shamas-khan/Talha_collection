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
        Schema::create('customer_accounts_details', function (Blueprint $table) {
            $table->id('customer_accounts_detail_id'); // Auto-incrementing primary key
            $table->unsignedBigInteger('customer_order_detail_id'); // Foreign key column
            $table->unsignedBigInteger('customer_id'); // Foreign key column
            $table->decimal('total_amount', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->decimal('paid_amount', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->decimal('remaining_amount', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->foreign('customer_order_detail_id')->references('customer_order_detail_id')->on('customer_order_detail'); // Foreign key constraint
            $table->foreign('customer_id')->references('customer_id')->on('customer'); // Foreign key constraint
            $table->timestamps(); // Created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_accounts_details');
    }
};
