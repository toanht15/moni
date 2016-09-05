<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToWelcomeMails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('welcome_mails', function(Blueprint $table)
		{
			$table->index('user_mail_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('welcome_mails', function(Blueprint $table)
		{
			$table->dropIndex('welcome_mails_user_mail_id_index');
		});
	}

}
