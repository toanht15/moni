<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableWithdraw extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('withdraw_logs', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('brand_user_relation_id')->unsigned();
			$table->boolean('withdraw_from_aaid_flg')->default(false);

			$table->tinyInteger('del_flg')->default(0);
			$table->timestamps();

			$table->foreign('brand_user_relation_id')->references('id')->on('brands_users_relations');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('withdraw_logs');
	}

}
