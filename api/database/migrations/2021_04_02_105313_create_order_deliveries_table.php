<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_deliveries', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('entrance', unsigned: true)->nullable();
            $table->smallInteger('floor', unsigned: true)->nullable();
            $table->smallInteger('apartment', unsigned: true)->nullable();
            $table->boolean('service_to_door')->default(false);
            $table->decimal('price', unsigned: true)->comment('Цена доставки');

            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_deliveries');
    }
};
