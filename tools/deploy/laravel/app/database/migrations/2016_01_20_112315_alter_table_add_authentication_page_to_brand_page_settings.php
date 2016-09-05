<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddAuthenticationPageToBrandPageSettings extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brand_page_settings', function(Blueprint $table)
		{
            $table->tinyInteger('age_authentication_flg')->default(0)->after('restricted_age');
            $table->text('authentication_page_content')->after('age_authentication_flg');
            $table->string('not_authentication_url',512)->after('authentication_page_content');
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
            $table->dropColumn('age_authentication_flg');
            $table->dropColumn('authentication_page_content');
            $table->dropColumn('not_authentication_url');
		});
	}
}
