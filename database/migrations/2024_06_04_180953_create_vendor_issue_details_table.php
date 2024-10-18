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
        Schema::create('vendor_issue_details', function (Blueprint $table) {
            $table->id('vendor_issue_detail_id'); // Auto-incrementing primary key
            $table->unsignedBigInteger('vendor_id'); // Foreign key column
            $table->unsignedBigInteger('issue_raw_material_id'); // Foreign key column
            $table->foreign('vendor_id')->references('vendor_id')->on('vendor'); // Foreign key constraint
            $table->foreign('issue_raw_material_id')->references('issue_raw_material_id')->on('issue_raw_material'); // Foreign key constraint
            $table->timestamps(); // Created_at and updated_at columns
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_issue_details');
    }
};
