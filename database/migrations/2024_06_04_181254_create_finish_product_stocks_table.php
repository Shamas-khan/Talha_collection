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
        Schema::create('finish_product_stocks', function (Blueprint $table) {
            $table->id('finish_product_stock_id'); // Auto-incrementing primary key
            $table->unsignedBigInteger('finish_product_id'); // Foreign key column
            $table->unsignedBigInteger('issue_raw_material_id'); // Foreign key column
            $table->integer('quantity'); // INT NOT NULL column
            $table->foreign('finish_product_id')->references('finish_product_id')->on('finish_product'); // Foreign key constraint
            $table->foreign('issue_raw_material_id')->references('issue_raw_material_id')->on('issue_raw_material'); // Foreign key constraint
            $table->timestamps(); // Created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finish_product_stocks');
    }
};
