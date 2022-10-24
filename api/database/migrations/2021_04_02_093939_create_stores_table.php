<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->json('schedule')->default('[]');
            $table->text('route')->nullable();
            $table->boolean('delivery')->default(false);
            $table->boolean('active')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('location_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
