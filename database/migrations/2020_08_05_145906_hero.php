<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Hero extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hero', function (Blueprint $table) {
            $table->id();
            $table->string('login', 20)->unique();
            $table->string('name', 100)->nullable();
            $table->integer('style')->unsigned()->default(10);
            $table->string('avatar', 255)->unique();
            $table->string('password', 60)->unique();
            $table->string('api_token', 60)->unique();
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
        Schema::dropIfExists('hero');
    }
}
