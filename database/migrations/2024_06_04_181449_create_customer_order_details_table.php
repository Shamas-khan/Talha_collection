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
        Schema::create('customer_order_details', function (Blueprint $table) {
            $table->id('customer_order_detail_id'); // Auto-incrementing primary key
            $table->unsignedBigInteger('customer_id'); // Foreign key column
            $table->unsignedBigInteger('sell_order_id'); // Foreign key column
            $table->foreign('customer_id')->references('customer_id')->on('customer'); // Foreign key constraint
            $table->foreign('sell_order_id')->references('sell_order_id')->on('sell_order'); // Foreign key constraint
            $table->timestamps(); // Created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_order_details');
    }
};
