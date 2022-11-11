<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->float('cost', unsigned: true);
            $table->json('statuses')->default('[]');
            $table->text('note')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->string('sber_id')->nullable();
            $table->string('yandex_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignUuid('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignUuid('store_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('payment_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('delivery_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
