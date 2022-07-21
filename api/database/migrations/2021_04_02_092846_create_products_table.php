<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->integer('code', unsigned: true)->unique();
            $table->string('barcode')->nullable();
            $table->text('description')->nullable();
            $table->boolean('marked')->default(false);
            $table->boolean('recipe')->default(false);
            $table->boolean('sale')->default(false);
            $table->tinyInteger('status', unsigned: true)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};