<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropCreatedAtAndUpdatedAtOfUserSearchInfo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_search_info', function(Blueprint $table)
		{
			$table->dropColumn('created_at');
			$table->dropColumn('updated_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_search_info', function(Blueprint $table)
		{
			$table->timestamps();
		});
	}

}