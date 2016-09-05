<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableFacebookMarketingSearchFanHistoriesAddSearchType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('facebook_marketing_search_fan_histories', function(Blueprint $table)
		{
			$table->tinyInteger('search_type')->default(0)->after('search_condition');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('facebook_marketing_search_fan_histories', function(Blueprint $table)
		{
			$table->dropColumn('search_type');
		});
	}
}
