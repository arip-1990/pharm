<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_statistics', function (Blueprint $table) {
            $table->id();
            $table->ipAddress('ip');
            $table->string('os');
            $table->string('browser');
            $table->string('city')->nullable();
            $table->string('screen')->nullable();
            $table->text('referrer')->nullable();
            $table->timestamps();

            $table->foreignUuid('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_statistics');
    }
};
