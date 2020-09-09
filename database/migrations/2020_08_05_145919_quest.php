<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Quest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quest', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id')->unsigned();
            $table->bigInteger('performer_id')->unsigned()->nullable();
            $table->bigInteger('gang_id')->unsigned();
            $table->string('title', 100);
            $table->string('description',255);
            $table->integer('base_reward')->unsigned();
            $table->integer('bonus_reward')->unsigned();
            $table->enum('state',['open', 'progress', 'pending', 'complete', 'declined'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quest');
    }
}
