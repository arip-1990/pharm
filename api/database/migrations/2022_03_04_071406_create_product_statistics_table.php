<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_statistics', function (Blueprint $table) {
            $table->integer('views', unsigned: true)->default(0);
            $table->integer('orders', unsigned: true)->default(0);
            $table->integer('reviews', unsigned: true)->default(0);
            $table->integer('cancellations', unsigned: true)->default(0);
            $table->decimal('rating', unsigned: true)->default(0);
            $table->timestamps();

            $table->primary('id');

            $table->foreignUuid('id')->constrained('products')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_statistics');
    }
};
