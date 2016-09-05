<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLogicType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_instant_win_actions', function(Blueprint $table)
		{
            $table->tinyInteger('logic_type')->default(1)->after('once_flg');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_instant_win_actions', function(Blueprint $table)
		{
            $table->dropColumn('logic_type');
        });
	}

}
