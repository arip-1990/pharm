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
            $table->string('title')->nullable()->comment('название');
            $table->text('description')->nullable()->nullable()->comment('описание');
            $table->string('type')->nullable()->comment('тип (delivery, pickup — доставка или самовывоз)');
            $table->decimal('price', 10, 2)->nullable()->comment('Минимальная цена необходимая для данного типа заявки');
            $table->tinyInteger('min')->nullable()->default(0)->comment('минимальный срок доставки');
            $table->tinyInteger('max')->nullable()->default(0)->comment('максимальный срок доставки');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
