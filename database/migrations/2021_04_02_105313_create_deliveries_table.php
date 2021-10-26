<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveriesTable extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->string('city');
            $table->string('street');
            $table->string('house');
            $table->string('entrance');
            $table->tinyInteger('floor');
            $table->string('apartment');
            $table->boolean('service_to_door')->default(false);
            $table->float('delivery_price', 8, 2, true);

            $table->primary('order_id');

            $table->foreignId('order_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
}
