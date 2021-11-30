<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('payment')->default(0);
            $table->boolean('delivery')->default(false);
            $table->float('cost', 8, 2, true);
            $table->string('status');
            $table->json('statuses')->default('[]');
            $table->text('note')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->string('sber_id')->nullable();
            $table->string('yandex_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('store_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}
