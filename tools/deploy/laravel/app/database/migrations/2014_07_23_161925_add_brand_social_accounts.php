<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBrandSocialAccounts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brand_social_accounts', function(Blueprint $table)
		{
			//
            $table->bigInteger('user_id')->unsigned()->after('brand_id')->default(1);
            $table->foreign('user_id')->references('id')->on('users');
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
            $table->dropForeign('brand_social_accounts_user_id_foreign');
            $table->dropColumn('user_id');
		});
	}

}
