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
            $table->tinyInteger('type', unsigned: true)->default(0);
            $table->string('street');
            $table->string('house');
            $table->json('coordinate')->default('[]');
            $table->string('prefix', 8)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['city_id', 'street', 'house']);

            $table->foreignId('city_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
