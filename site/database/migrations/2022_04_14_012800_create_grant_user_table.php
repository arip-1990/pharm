<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrantUserTable extends Migration
{
    public function up(): void
    {
        Schema::create('grant_user', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('grant_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grant_user');
    }
}
