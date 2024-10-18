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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id('expense_id'); // Auto-incrementing primary key
            $table->unsignedBigInteger('expense_category_id'); // Foreign key column
            $table->string('reason', 255)->nullable(false); // VARCHAR(255) NOT NULL column
            $table->decimal('amount', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->text('note')->nullable(); // TEXT column, nullable
            $table->timestamps(); // Created_at and updated_at columns

            // Foreign key constraint
            $table->foreign('expense_category_id')->references('expense_category_id')->on('expense_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
