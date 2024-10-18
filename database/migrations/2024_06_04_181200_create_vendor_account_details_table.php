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
        Schema::create('vendor_account_details', function (Blueprint $table) {
            $table->id('vendor_account_detail_id'); // Auto-incrementing primary key
            $table->unsignedBigInteger('vendor_id'); // Foreign key column
            $table->unsignedBigInteger('vendor_issue_detail_id'); // Foreign key column
            $table->decimal('total_amount', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->decimal('remaining_amount', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->decimal('paid_amount', 10, 2); // DECIMAL(10, 2) NOT NULL column
            $table->foreign('vendor_id')->references('vendor_id')->on('vendor'); // Foreign key constraint
            $table->foreign('vendor_issue_detail_id')->references('vendor_issue_detail_id')->on('vendor_issue_detail'); // Foreign key constraint
            $table->timestamps(); // Created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_account_details');
    }
};
