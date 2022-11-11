<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->string('name', 16);
            $table->string('title');
            $table->string('extension', 6);
            $table->smallInteger('type', unsigned: true)->default(0);
            $table->smallInteger('sort', unsigned: true)->default(0);
            $table->smallInteger('status', unsigned: true)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
