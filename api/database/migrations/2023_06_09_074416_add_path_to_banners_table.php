<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('path')->after('picture')->default('/');
            $table->string('link')->after('path')->nullable();
            $table->string('type', 32)->after('link')->default('main');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['type', 'link', 'path']);
        });
    }
};
