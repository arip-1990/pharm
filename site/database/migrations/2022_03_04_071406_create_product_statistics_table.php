<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStatisticsTable extends Migration
{
    public function up(): void
    {
        Schema::create('product_statistics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('views', unsigned: true)->default(0);
            $table->integer('orders', unsigned: true)->default(0);
            $table->integer('cancellations', unsigned: true)->default(0);
            $table->float('rating', unsigned: true)->default(0);
            $table->timestamps();

            $table->foreign('id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_statistics');
    }
}
