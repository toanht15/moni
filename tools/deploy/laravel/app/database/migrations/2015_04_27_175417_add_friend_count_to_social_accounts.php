<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFriendCountToSocialAccounts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('social_accounts', function(Blueprint $table)
		{
			$table->integer("friend_count")->nullable()->after("validated");
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
			$table->dropColumn("friend_count");
		});
	}

}
