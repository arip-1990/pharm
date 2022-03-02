<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivesTable extends Migration
{
    public function up(): void
    {
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id', unsigned: true);
            $table->integer('code', unsigned: true);
            $table->string('name');
            $table->float('price', unsigned: true);
            $table->smallInteger('quantity', unsigned: true);
            $table->timestamps();

            $table->unique(['order_id', 'code']);

            $table->foreignUuid('store_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
}
