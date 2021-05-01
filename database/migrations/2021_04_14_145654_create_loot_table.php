<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLootTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up() {
        Schema::create('loot', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->decimal('price', 12, 2)->default(100.0);
            $table->text('detail')->nullable();
            $table->boolean('stock')->nullable()->default(true);
            $table->unsignedBigInteger('box_id')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */

    public function down() {
        Schema::dropIfExists('loot');
    }
}
