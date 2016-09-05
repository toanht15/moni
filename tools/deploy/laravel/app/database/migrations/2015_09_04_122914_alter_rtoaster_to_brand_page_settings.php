<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRtoasterToBrandPageSettings extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brand_page_settings', function(Blueprint $table)
		{
            $table->string('rtoaster', 511)->default('')->after('favicon_url');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('brand_page_settings', function(Blueprint $table)
		{
            $table->dropColumn('rtoaster');
		});
	}

}
