<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuctionLotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auction_lots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loot_id');
            $table->decimal('price', 12, 2);
            $table->integer('stap');
            $table->decimal('start', 12, 2);
            $table->string('rate')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('updated')->nullable();
            $table->string('finished')->nullable();
            $table->integer('finish')->nullable();
            $table->timestamps();
        });

        Schema::table('auction_lots', function(Blueprint $table) {
            $table->foreign('loot_id')->references('id')->on('loot');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auction_lots');
    }
}
