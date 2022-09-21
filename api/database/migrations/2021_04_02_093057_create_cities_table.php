<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('prefix', 8);
            $table->smallInteger('type', unsigned: true)->default(0);
            $table->timestamps();

            $table->unique(['name', 'type']);

            $table->foreignId('parent_id')->nullable()->constrained('cities')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
