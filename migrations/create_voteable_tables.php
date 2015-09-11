<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVoteableTables extends Migration
{

    public function up()
    {

        Schema::create('voteable', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('voteable');
            $table->string('user_id', 36)->index();
            $table->smallInteger('value');
            $table->timestamps();

            $table->unique(['voteable_id', 'voteable_type', 'user_id']);
        });

        Schema::create('voteable_counter', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('voteable');
            $table->integer('up')->unsigned()->default(0);
            $table->integer('down')->unsigned()->default(0);

            $table->unique(['voteable_id', 'voteable_type']);
        });

    }

    public function down()
    {
        Schema::drop('voteable');
        Schema::drop('voteable_counter');
    }
}
