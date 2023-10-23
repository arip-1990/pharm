<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('inn')->nullable();
            $table->string('ogrn')->nullable();
            $table->string('license')->nullable();
            $table->string('address')->nullable();
            $table->string('website')->nullable();
            $table->timestamps();
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });

        Schema::dropIfExists('companies');
    }
};
