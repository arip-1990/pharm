<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->string('file');
            $table->smallInteger('type', unsigned: true)->default(0);
            $table->smallInteger('sort', unsigned: true)->default(0);
            $table->smallInteger('status', unsigned: true)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreignUuid('product_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
