<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCpPaymentActions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_payment_actions', function(Blueprint $table)
		{
			$table->boolean('skip_flg')->after('finish_html_content');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_payment_actions', function(Blueprint $table)
		{
			$table->dropColumn('skip_flg');
		});
	}

}
