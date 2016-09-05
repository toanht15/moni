<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterBrandPageSettingTableAddTopPageOgUrl extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brand_page_settings', function(Blueprint $table)
		{
			$table->string('top_page_og_url', 511)->after('top_page_url');
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
			$table->dropColumn('top_page_og_url');
		});
	}

}
