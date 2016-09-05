<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueToSocialAccounts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('social_accounts', function(Blueprint $table)
		{
			$table->unique(array('user_id', 'social_media_id', 'social_media_account_id'), 'social_accounts_unique');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('social_accounts', function(Blueprint $table)
		{
			$table->dropForeign('social_accounts_user_id_foreign');
			$table->dropUnique('social_accounts_unique');
			$table->foreign('user_id')->references('id')->on('users');
		});
	}

}
