<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableFacebookMarketingAccountsAddType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('facebook_marketing_accounts', function(Blueprint $table)
		{
            $table->tinyInteger('type')->after('web_custom_audience_tos');
		});
	}
    
	/**
     *
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('facebook_marketing_accounts', function(Blueprint $table)
        {
            $table->dropColumn('type');
		});
	}
}
