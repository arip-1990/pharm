<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('string');
            $table->string('default')->nullable();
            $table->boolean('required')->default(false);
            $table->json('variants')->default('[]');
            $table->timestamps();

            $table->foreignId('category_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
