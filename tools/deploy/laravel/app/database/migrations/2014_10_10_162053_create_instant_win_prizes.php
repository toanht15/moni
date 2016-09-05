<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstantWinPrizes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('instant_win_prizes', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('cp_instant_win_action_id')->unsigned();
            $table->string('image_url', 512)->default('');
            $table->tinyInteger('image_type')->default(0);
            $table->longText('text');
            $table->tinyInteger('prize_status')->default(0);
            $table->boolean('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_instant_win_action_id', 'instant_win_prizes_cp_instant_win_action_id_foreign')->references('id')->on('cp_instant_win_actions');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('instant_win_prizes');
	}

}
