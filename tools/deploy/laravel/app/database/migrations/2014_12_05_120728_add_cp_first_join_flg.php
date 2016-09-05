<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCpFirstJoinFlg extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('cps', function(Blueprint $table)
        {
            $table->boolean('announce_type')->default(false)->after('type');
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
            $table->dropColumn('announce_type');
        });
	}

}
