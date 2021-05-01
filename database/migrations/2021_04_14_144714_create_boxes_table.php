<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateBoxesTable extends Migration

{

    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up() {
        Schema::create('boxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->integer('views')->default(0);
            $table->integer('opens')->default(0);

            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('last_gift_id')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */

    public function down() {
        Schema::dropIfExists('boxes');
    }
}
