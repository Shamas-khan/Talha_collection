<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('finish_products', function (Blueprint $table) {
            $table->id('finish_product_id'); // Auto-incrementing primary key
            $table->unsignedBigInteger('raw_material_id'); // Foreign key column
            $table->string('product_name'); // VARCHAR(255) NOT NULL column
            $table->foreign('raw_material_id')->references('raw_material_id')->on('raw_material'); // Foreign key constraint
            $table->timestamps(); // Created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finish_products');
    }
};
