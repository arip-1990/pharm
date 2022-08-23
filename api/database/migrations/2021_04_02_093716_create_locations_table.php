<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->json('coordinate')->default('[]');
            $table->string('prefix', 8)->nullable();
            $table->tinyInteger('type', unsigned: true)->nullable();
            $table->timestamps();

            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->foreignId('street_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
