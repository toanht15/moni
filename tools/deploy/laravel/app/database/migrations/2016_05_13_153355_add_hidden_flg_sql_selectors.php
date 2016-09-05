<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHiddenFlgSqlSelectors extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sql_selectors', function(Blueprint $table)
		{
            $table->boolean('hidden_flg')->after('author')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sql_selectors', function(Blueprint $table)
		{
            $table->dropColumn('hidden_flg');
		});
	}

}
