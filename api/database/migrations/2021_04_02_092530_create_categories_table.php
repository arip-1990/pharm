<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->text('description')->nullable();
            $table->smallInteger('sort', unsigned: true)->default(0);
            $table->string('picture')->nullable();
            $table->integer('_lft', unsigned: true);
            $table->integer('_rgt', unsigned: true);
            $table->timestamps();

            $table->foreignId('parent_id')->nullable()->constrained('categories')->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
