<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBeginnerFlgToCpUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_users', function(Blueprint $table) {
			$table->boolean('beginner_flg')->after('referrer')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_users', function(Blueprint $table) {
			$table->dropColumn('beginner_flg');
		});
	}

}
