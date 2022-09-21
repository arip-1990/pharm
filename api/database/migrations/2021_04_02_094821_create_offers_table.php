<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', unsigned: true)->default(0);
            $table->integer('quantity', unsigned: true)->default(0);
            $table->timestamps();

            $table->unique(['store_id', 'product_id']);

            $table->foreignUuid('store_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('product_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
