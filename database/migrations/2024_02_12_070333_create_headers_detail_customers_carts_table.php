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
        Schema::create('headers_detail_customers_carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_cart_id');
            $table->foreign('customer_cart_id')->references('id')->on('headers_customers_carts');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->integer('quantity');
            $table->integer('sub_total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('headers_detail_customers_carts');
    }
};
