<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntries extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('entries', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('campaign_action_id')->unsigned();
			$table->string('image_url', 512)->default('');
			$table->longText('text');
			$table->string('button_label_text')->default('応募する');
			$table->tinyInteger('del_flg')->default(0);
			$table->foreign('campaign_action_id')->references('id')->on('entries');
			$table->unique('campaign_action_id');
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
		Schema::drop('entries');
	}

}
