<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickupLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_locations', function (Blueprint $table) {
            $table->id();
            $table->string('slug_id')->comment('идентификатор самовывоза, принимает символы');
            $table->string('title')->comment('название');
            $table->string('address')->comment('адрес');
            $table->string('city')->comment('город');
            $table->string('time')->nullable()->comment('расписание / время работы');
            $table->string('subway')->nullable()->comment('ближайшая станция метро');
            $table->string('mall')->nullable()->comment('название ТЦ, если не указывается в поле title');
            $table->decimal('lat', 10, 7)->nullable()->comment('широта для отображения на карте');
            $table->decimal('lon', 10, 7)->nullable()->comment('долгота для отображения на карте');
            $table->decimal('price', 20, 2)->comment('цена доставки в этот ПВЗ');
            $table->unsignedInteger('min')->comment('ожидаемое время доставки (0 - сегодня)');
            $table->string('notice')->nullable()->comment('выделенный текстовый блок, например, чтобы уведомить покупателя, что не все товары доступны в данном ПВЗ');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pickup_locations');
    }
}
