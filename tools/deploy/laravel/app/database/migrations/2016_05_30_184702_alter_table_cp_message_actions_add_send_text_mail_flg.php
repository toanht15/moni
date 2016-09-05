<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCpMessageActionsAddSendTextMailFlg extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_message_actions', function(Blueprint $table)
		{
			$table->tinyInteger('send_text_mail_flg')->default(0)->after('manual_step_flg');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_message_actions', function(Blueprint $table)
		{
			$table->dropColumn('send_text_mail_flg');
		});
	}

}
