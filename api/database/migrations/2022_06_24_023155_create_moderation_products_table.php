<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moderation_products', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status', unsigned: true)->default(0);
            $table->string('type');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreignUuid('product_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('user_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderation_products');
    }
};
