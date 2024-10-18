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
        Schema::create('supplier_accounts', function (Blueprint $table) {
            $table->id('supplier_account_id'); // Auto-incrementing primary key
            $table->unsignedBigInteger('supplier_id'); // Foreign key column
            $table->unsignedBigInteger('purchase_id'); // Foreign key column
            $table->decimal('total_amount', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->decimal('paid_amount', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->decimal('remaining_amount', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->foreign('supplier_id')->references('supplier_id')->on('supplier'); // Foreign key constraint
            $table->foreign('purchase_id')->references('purchase_id')->on('purchase'); // Foreign key constraint
            $table->timestamps(); // Created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_accounts');
    }
};
