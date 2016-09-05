<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJoinLimitFlgToCpsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cps', function(Blueprint $table)
		{
            $table->tinyInteger('join_limit_flg')->default(0)->after("image_url");
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
            $table->dropColumn("join_limit_flg");
		});
	}

}
