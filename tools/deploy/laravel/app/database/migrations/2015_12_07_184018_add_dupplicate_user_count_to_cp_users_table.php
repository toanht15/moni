<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDupplicateUserCountToCpUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_users', function(Blueprint $table)
		{
            $table->integer('duplicate_address_count')->default(0)->after('join_sns');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_users', function(Blueprint $table)
		{
            $table->dropColumn('duplicate_address_count');
		});
	}
}
