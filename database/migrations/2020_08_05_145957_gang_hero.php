<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GangHero extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gang_hero', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('gang_id')->unsigned();
            $table->bigInteger('hero_id')->unsigned();
            $table->tinyInteger('creator')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gang_hero');
    }
}
