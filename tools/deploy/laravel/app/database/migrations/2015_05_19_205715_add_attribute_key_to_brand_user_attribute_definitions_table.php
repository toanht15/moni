<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAttributeKeyToBrandUserAttributeDefinitionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brand_user_attribute_definitions', function(Blueprint $table)
		{
            $table->string('attribute_key',30)->default('')->after('brand_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('brand_user_attribute_definitions', function(Blueprint $table)
		{
            $table->dropColumn('attribute_key');
		});
	}

}
