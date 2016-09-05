<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEngagementLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('engagement_logs', function(Blueprint $table)
		{
			$table->bigIncrements('id');
            $table->integer('cp_action_id')->unsigned();
            $table->integer('cp_user_id')->unsigned();
            $table->integer('brand_social_account_id')->unsigned();
            $table->tinyInteger('del_flg')->default(0);;
            $table->timestamps();

            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->foreign('cp_user_id')->references('id')->on('cp_users');
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
		Schema::drop('engagement_logs');
	}

}
