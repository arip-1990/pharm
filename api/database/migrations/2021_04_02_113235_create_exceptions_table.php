<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exceptions', function (Blueprint $table) {
            $table->id();
            $table->string('initiator');
            $table->string('initiator_id')->nullable();
            $table->string('type');
            $table->boolean('fixed')->default(false);
            $table->text('message');
            $table->timestamps();
            $table->timestamp('decided_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exceptions');
    }
};
