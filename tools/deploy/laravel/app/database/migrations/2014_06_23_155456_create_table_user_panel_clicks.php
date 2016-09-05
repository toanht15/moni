<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserPanelClicks extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_panel_clicks', function(Blueprint $t)
		{
			$t->increments('id');
			$t->bigInteger('user_id')->unsigned();
			$t->string('entries',30)->default('');
			$t->Integer('entries_id')->unsigned();
			$t->tinyInteger('del_flg')->default(0);
			// created_at, updated_at DATETIME
			$t->timestamps();
			$t->index('user_id');
			$t->index(array('entries','entries_id'));
			$t->foreign('user_id')->references('id')->on('users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_panel_clicks');
	}

}
