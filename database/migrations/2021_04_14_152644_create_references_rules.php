<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferencesRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boxes', function(Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('last_gift_id')->references('id')->on('loot');
        });

        Schema::table('feed', function(Blueprint $table) {
            $table->foreign('loot_id')->references('id')->on('loot');
            $table->foreign('box_id')->references('id')->on('boxes');
        });

        Schema::table('loot', function(Blueprint $table) {
            $table->foreign('box_id')->references('id')->on('boxes');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('references_rules');
    }
}
