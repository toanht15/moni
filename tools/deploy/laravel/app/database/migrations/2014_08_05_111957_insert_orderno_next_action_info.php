<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertOrdernoNextActionInfo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('cp_next_action_info', function(Blueprint $table)
        {
            $table->tinyInteger('order_no')->default(0)->after('label');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('cp_next_action_info', function(Blueprint $table) {
            $table->dropColumn('order_no');
        });
	}

}
