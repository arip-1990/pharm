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
        Schema::create('number_of_childrens', function (Blueprint $table) {
            $table->id();
            $table->integer('children')->default(0);
            $table->foreignUuid('user_id')->unique()->constrained()->cascadeOnDelete();        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('number_of_childrens');
    }
};
