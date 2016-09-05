<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCpLostNotifications extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cp_lost_notifications', function (Blueprint $table) {
            //auto increment key (primary key)
            $table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->tinyInteger('notified');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->unique('cp_action_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cp_lost_notifications');
    }

}