<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMessageActions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('message_actions', function(Blueprint $table)
		{
            $table->increments('id');
            $table->integer('campaign_action_id')->unsigned();
            $table->string('image_url', 512)->default('');
            $table->longText('text');
            $table->tinyInteger('del_flg')->default(0);
            $table->foreign('campaign_action_id')->references('id')->on('campaign_actions');
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
		Schema::drop('message_actions');
	}

}
