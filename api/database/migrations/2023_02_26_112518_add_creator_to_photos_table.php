<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->foreignUuid('creator_id')->nullable()->constrained('users')->onUpdate('cascade')->nullOnDelete();
            $table->foreignUuid('destroyer_id')->nullable()->constrained('users')->onUpdate('cascade')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn(['destroyer_id', 'creator_id']);
        });
    }
};
