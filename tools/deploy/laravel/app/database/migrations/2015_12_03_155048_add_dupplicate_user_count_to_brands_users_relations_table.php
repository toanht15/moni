<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDupplicateUserCountToBrandsUsersRelationsTable extends Migration {
    
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brands_users_relations', function(Blueprint $table)
		{
            $table->integer('duplicate_address_count')->default(0)->after('login_count');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('brands_users_relations', function(Blueprint $table)
		{
            $table->dropColumn('duplicate_address_count');
		});
	}
}
