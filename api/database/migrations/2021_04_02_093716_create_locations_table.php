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
            $table->smallInteger('type', unsigned: true)->default(0);
            $table->string('prefix', 8)->default('ул');
            $table->string('street');
            $table->string('house')->default('1');
            $table->json('coordinate')->default('[]');
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
