<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuestHero extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quest_hero', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('quest_id')->unsigned();
            $table->bigInteger('hero_id')->unsigned();
            $table->enum('type',['customer','performer']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quest_hero');
    }
}
