<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateLoggedToSocialAccountFollowersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('social_account_followers', function(Blueprint $table)
		{
            $table->date('date_logged')->default('0000-00-00')->after('id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('social_account_followers', function(Blueprint $table)
		{
            $table->dropColumn('date_logged');
		});
	}

}
