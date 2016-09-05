<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpShareUserLogsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cp_share_user_logs', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('cp_user_id')->unsigned();
            $table->integer('cp_share_action_id')->unsigned();
            $table->integer('type')->unsigned();
            $table->tinyInteger('del_flg')->default(0);
            $table->foreign('cp_user_id')->references('id')->on('cp_users');
            $table->foreign('cp_share_action_id')->references('id')->on('cp_share_actions');
            $table->unique(array('cp_user_id', 'cp_share_action_id'));
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
        Schema::drop('cp_share_user_logs');
    }

}
