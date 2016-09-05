<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterChangeBrandUserAttributeDefinitions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

        DB::statement('ALTER TABLE `brand_user_attribute_definitions` CHANGE `key` `attribute_name` varchar(30)');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement('ALTER TABLE `brand_user_attribute_definitions` CHANGE `attribute_name` `key` varchar(30)');
	}

}
