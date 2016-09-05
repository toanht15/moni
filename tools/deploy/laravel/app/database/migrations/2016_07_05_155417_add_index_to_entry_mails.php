<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToEntryMails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('entry_mails', function(Blueprint $table)
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
		Schema::table('entry_mails', function(Blueprint $table)
		{
			$table->dropIndex('entry_mails_user_mail_id_index');
		});
	}
}
