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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->unique(); // p001
            $table->string('name');
            $table->unsignedBigInteger('category_id');
            $table->integer('price');
            $table->integer('originalPrice')->nullable();
            $table->string('weight')->nullable();
            $table->float('rating')->default(0);
            $table->integer('reviews')->default(0);
            $table->string('brand')->nullable();
            $table->integer('discount')->default(0);
            $table->string('bgColor')->nullable();
            $table->integer('quantity')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
