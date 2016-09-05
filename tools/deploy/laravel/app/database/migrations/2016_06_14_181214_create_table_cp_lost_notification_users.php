<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCpLostNotificationUsers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cp_lost_notification_users', function (Blueprint $table) {
            //auto increment key (primary key)
            $table->increments('id');
            $table->integer('cp_lost_notification_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('cp_lost_notification_id')->references('id')->on('cp_lost_notifications');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(array('cp_lost_notification_id', 'user_id'), 'cp_lost_notification_users_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cp_lost_notification_users');
    }

}