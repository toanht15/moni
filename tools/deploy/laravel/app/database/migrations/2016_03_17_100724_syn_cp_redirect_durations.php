<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SynCpRedirectDurations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('syn_cp_redirect_durations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('syn_cp_id')->unsigned();
			$table->timestamp('start_at')->default('0000-00-00 00:00:00');
			$table->timestamp('end_at')->default('0000-00-00 00:00:00');
			$table->boolean('del_flg')->default(0);
			$table->timestamps();

			$table->foreign('syn_cp_id')->references('id')->on('syn_cps');
			$table->index('start_at');
			$table->index('end_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('syn_cp_redirect_durations');
	}

}
