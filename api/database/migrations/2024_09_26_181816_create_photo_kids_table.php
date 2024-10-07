<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('photo_kids', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('link');
            $table->string('photo_name')->nullable(false);
            $table->date('birthdate')->nullable(false);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->boolean('published')->default(false);
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_kids');
    }
};
