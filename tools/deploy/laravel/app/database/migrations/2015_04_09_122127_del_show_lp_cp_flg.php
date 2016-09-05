<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DelShowLpCpFlg extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('cps', function(Blueprint $t)
        {
            $t->dropColumn('show_lp_flg');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('cps', function($t)
        {
            $t->tinyInteger('show_lp_flg')->default(1)->after('status');
        });
	}

}
