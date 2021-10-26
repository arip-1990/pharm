<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatisticsTable extends Migration
{
    public function up(): void
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->string('ip');
            $table->string('city')->nullable();
            $table->string('os');
            $table->string('browser');
            $table->string('screen');
            $table->string('referrer')->nullable();
            $table->timestamps();

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistics');
    }
}
