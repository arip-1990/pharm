<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_product', function (Blueprint $table) {
            $table->id();
            $table->unique(['discount_id', 'product_id']);

            $table->foreignId('discount_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('product_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('discounts', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expired_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            Schema::dropIfExists('discount_product');

            $table->dropColumn(['expired_at', 'started_at']);
        });
    }
};
