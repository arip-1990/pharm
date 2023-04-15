<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignUuid('editor_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::table('values', function (Blueprint $table) {
            $table->foreignUuid('editor_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->string('file', 32)->after('title')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn('file');
        });

        Schema::table('values', function (Blueprint $table) {
            $table->dropColumn('editor_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('editor_id');
        });
    }
};
