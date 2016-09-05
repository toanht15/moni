<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBackMoniplaFlgToCpTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cps', function(Blueprint $table)
		{
			$table->tinyInteger('back_monipla_flg')->after('show_monipla_com_flg')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cps', function(Blueprint $table)
		{
			$table->dropColumn('back_monipla_flg');
		});
	}

}
