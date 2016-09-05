<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeBrandUserAttributeDefinitions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('brand_user_attribute_definitions', function(Blueprint $t)
		{
			$t->increments('id');
			$t->integer('brand_id')->unsigned();
			$t->string('key', 30);
			$t->tinyInteger('attribute_type');
			$t->string('value_set', 100)->default('');
			$t->foreign('brand_id')->references('id')->on('brands');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('brand_user_attribute_definitions');
	}

}
