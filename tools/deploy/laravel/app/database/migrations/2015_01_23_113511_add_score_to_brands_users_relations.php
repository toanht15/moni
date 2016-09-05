<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScoreToBrandsUsersRelations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brands_users_relations', function(Blueprint $table)
		{
            $table->integer('score')->unsigned()->default(false)->after('no');
            $table->index('score');
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
            $table->dropColumn('score');
		});
	}

}
