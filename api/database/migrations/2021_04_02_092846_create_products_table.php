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
            $table->text('description')->nullable();
            $table->json('barcodes')->default('[]');
            $table->boolean('marked')->default(false);
            $table->boolean('recipe')->default(false);
            $table->smallInteger('status', unsigned: true)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('category_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('discount_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
