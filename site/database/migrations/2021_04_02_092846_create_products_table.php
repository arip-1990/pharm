<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('code', false, true)->unique();
            $table->string('barcode')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('marked')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}
