<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BrandSocialAccountsUniqueKey extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('brand_social_accounts', function(Blueprint $table)
        {
            $table->dropUnique('brand_social_accounts_unique_key');
            $table->unique(array('social_media_account_id','brand_id'), 'brand_social_accounts_unique_key');
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
            $table->dropUnique('brand_social_accounts_unique_key');
            $table->unique(array('social_media_account_id','social_app_id'), 'brand_social_accounts_unique_key');
        });
	}

}
