<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManualStepFlgToCpMessageActions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_message_actions', function(Blueprint $table)
		{
			$table->boolean('manual_step_flg')->after('html_content')->default(0);
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
			$table->dropColumn('manual_step_flg');
		});
	}

}
