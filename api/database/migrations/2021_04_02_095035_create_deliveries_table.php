<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('pickup')->comment('delivery, pickup — доставка или самовывоз');
            $table->decimal('price', unsigned: true)->default(0)->comment('Минимальная цена необходимая для данного типа заявки');
            $table->smallInteger('min', unsigned: true)->default(0)->comment('минимальный срок доставки');
            $table->smallInteger('max', unsigned: true)->default(0)->comment('максимальный срок доставки');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
