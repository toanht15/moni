<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFangate extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('cp_fangate_actions', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->unique('cp_action_id');
        });

        Schema::create('fangate_social_accounts', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('cp_fangate_action_id')->unsigned();
            $table->integer('brand_social_account_id')->unsigned();
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('cp_fangate_action_id')->references('id')->on('cp_fangate_actions');
            $table->foreign('brand_social_account_id')->references('id')->on('brand_social_accounts');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('fangate_social_accounts');
        Schema::drop('cp_fangate_actions');
	}

}
