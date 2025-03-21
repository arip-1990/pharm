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
            $table->smallInteger('status', unsigned: true)->default(0);
            $table->string('type');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreignUuid('product_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderation_products');
    }
};
