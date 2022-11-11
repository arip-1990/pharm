<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photo_product', function (Blueprint $table) {
            $table->id();

            $table->unique(['photo_id', 'product_id']);

            $table->foreignId('photo_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('product_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photo_product');
    }
};
