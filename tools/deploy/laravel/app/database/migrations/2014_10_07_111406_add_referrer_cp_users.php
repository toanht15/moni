<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReferrerCpUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('cp_users', function(Blueprint $t)
        {
            $t->string('referrer', 255)->default('')->after('user_id');
            $t->string('from_id', 255)->default('')->after('user_id');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('cp_users', function(Blueprint $t)
        {
            $t->dropColumn('referrer');
            $t->dropColumn('from_id');
        });
	}

}
