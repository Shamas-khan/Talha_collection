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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('supplier_id'); // Auto-incrementing primary key
            $table->string('name'); // VARCHAR(255) NOT NULL column
            $table->string('contact', 20)->nullable(); // VARCHAR(20) column, nullable
            $table->string('address')->nullable(); // VARCHAR(255) column, nullable
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
        Schema::dropIfExists('suppliers');
    }
};