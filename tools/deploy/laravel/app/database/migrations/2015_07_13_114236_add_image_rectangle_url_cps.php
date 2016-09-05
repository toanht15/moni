<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageRectangleUrlCps extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cps', function(Blueprint $table)
		{
			$table->string('image_rectangle_url', 255)->default('0')->after('image_url');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cps', function(Blueprint $table)
		{
			$table->dropColumn('image_rectangle_url');
		});
	}

}
