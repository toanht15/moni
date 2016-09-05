<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterShareFlg extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cps', function(Blueprint $table)
		{
            $table->tinyInteger('share_flg')->default(1)->after("join_limit_flg");
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
            $table->dropColumn('share_flg');
		});
	}

}
