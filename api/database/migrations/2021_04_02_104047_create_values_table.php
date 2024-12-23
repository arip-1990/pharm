<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('values', function (Blueprint $table) {
            $table->id();
            $table->text('value');
            $table->timestamps();

            $table->unique(['attribute_id', 'product_id']);

            $table->foreignId('attribute_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('product_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('values');
    }
};
