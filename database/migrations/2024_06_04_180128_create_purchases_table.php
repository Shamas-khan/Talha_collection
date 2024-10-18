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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id('purchase_id'); // Auto-incrementing primary key
            $table->unsignedBigInteger('supplier_id'); // Foreign key column
            $table->unsignedBigInteger('raw_material_id'); // Foreign key column
            $table->unsignedBigInteger('unit_id'); // Foreign key column
            $table->integer('quantity'); // INT NOT NULL column
            $table->decimal('product_total', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->decimal('transportation_amount', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->decimal('grand_total', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->decimal('total_paid', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->decimal('remaining_amount', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers'); // Foreign key constraint
            $table->foreign('raw_material_id')->references('raw_material_id')->on('raw_materials'); // Foreign key constraint
            $table->foreign('unit_id')->references('unit_id')->on('units'); // Foreign key constraint
            $table->timestamps(); // Created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
};
