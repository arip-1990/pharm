<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLimitsTable extends Migration
{
    public function up(): void
    {
        Schema::create('limits', function (Blueprint $table) {
            $table->string('ip')->primary();
            $table->tinyInteger('limit', false, true);
            $table->integer('requests', false, true)->default(0);
            $table->timestamp('last_request')->nullable();
            $table->timestamp('expires')->nullable();

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('limits');
    }
}
