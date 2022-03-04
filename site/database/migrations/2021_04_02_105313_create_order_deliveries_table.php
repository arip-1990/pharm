<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDeliveriesTable extends Migration
{
    public function up(): void
    {
        Schema::create('order_deliveries', function (Blueprint $table) {
            $table->id('order_id');
            $table->string('city');
            $table->string('street');
            $table->string('house');
            $table->smallInteger('entrance', unsigned: true);
            $table->smallInteger('floor', unsigned: true);
            $table->smallInteger('apartment', unsigned: true);
            $table->boolean('service_to_door')->default(false);
            $table->float('delivery_price', unsigned: true);

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_deliveries');
    }
}
