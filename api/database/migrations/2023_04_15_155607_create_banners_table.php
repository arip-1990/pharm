<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('picture', 32)->unique();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreignUuid('creator_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('destroyer_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->unsignedSmallInteger('status')->default(0);
        });

        Schema::dropIfExists('banners');
    }
};
