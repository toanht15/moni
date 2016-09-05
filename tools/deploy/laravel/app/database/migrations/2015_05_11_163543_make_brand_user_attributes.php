<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeBrandUserAttributes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('brand_user_attributes', function(Blueprint $t)
		{
			$t->increments('id');
			$t->integer('definition_id')->unsigned();
			$t->bigInteger('user_id')->unsigned();
			$t->string('value', 100)->default('');
			$t->foreign('definition_id')->references('id')->on('brand_user_attribute_definitions');
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
		Schema::drop('brand_user_attributes');
	}

}
