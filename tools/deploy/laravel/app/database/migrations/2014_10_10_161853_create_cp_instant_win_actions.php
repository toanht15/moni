<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpInstantWinActions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cp_instant_win_actions', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->string('title', 255)->default('');
            $table->longText('text');
            $table->decimal('winning_rate', 6, 3)->default(0);
            $table->integer('time_value')->default(0);
            $table->tinyInteger('time_measurement')->default(0);
            $table->tinyInteger('once_flg')->default(0);
            $table->boolean('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_action_id', 'cp_instant_win_actions_cp_action_id_foreign')->references('id')->on('cp_actions');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cp_instant_win_actions');
	}

}