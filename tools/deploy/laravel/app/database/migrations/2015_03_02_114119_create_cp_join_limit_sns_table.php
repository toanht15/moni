<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpJoinLimitSnsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cp_join_limit_sns', function(Blueprint $table)
		{
            $table->increments('id');
            $table->integer('cp_id')->unsigned();
            $table->integer('social_media_id');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_id')->references('id')->on('cps');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cp_join_limit_sns');
	}

}
