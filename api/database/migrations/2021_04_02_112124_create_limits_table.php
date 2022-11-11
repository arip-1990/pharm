<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('limits', function (Blueprint $table) {
            $table->ipAddress('ip')->primary();
            $table->smallInteger('limit', unsigned: true);
            $table->smallInteger('requests', unsigned: true)->default(0);
            $table->timestamp('last_request')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->foreignUuid('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('limits');
    }
};
