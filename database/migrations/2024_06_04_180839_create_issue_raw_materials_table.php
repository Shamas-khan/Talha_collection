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
        Schema::create('issue_raw_materials', function (Blueprint $table) {
            $table->id('issue_raw_material_id'); // Auto-incrementing primary key
            $table->unsignedBigInteger('vendor_id'); // Foreign key column
            $table->unsignedBigInteger('raw_stock_id'); // Foreign key column
            $table->unsignedBigInteger('finish_product_id'); // Foreign key column
            $table->integer('issue_quantity'); // INT NOT NULL column
            $table->decimal('per_piece_rate', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->integer('total_product_quantity'); // INT NOT NULL column
            $table->integer('receive_quantity'); // INT NOT NULL column
            $table->integer('remaining_quantity'); // INT NOT NULL column
            $table->foreign('vendor_id')->references('vendor_id')->on('vendor'); // Foreign key constraint
            $table->foreign('raw_stock_id')->references('raw_stock_id')->on('raw_stock'); // Foreign key constraint
            $table->foreign('finish_product_id')->references('finish_product_id')->on('finish_product'); // Foreign key constraint
            $table->timestamps(); // Created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_raw_materials');
    }
};
