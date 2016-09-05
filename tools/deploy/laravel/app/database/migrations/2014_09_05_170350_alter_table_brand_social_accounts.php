<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableBrandSocialAccounts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brand_social_accounts', function(Blueprint $table)
		{
            $table->string('about',255)->default('')->after('screen_name');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('brand_social_accounts', function(Blueprint $table)
		{
            $table->dropColumn('about');
		});
	}

}
