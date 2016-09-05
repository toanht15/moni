<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterContentApiCodes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('content_api_codes', function(Blueprint $table)
		{
			$table->tinyInteger('cp_action_type')->after('cp_id')->default(0);
		});

        DB::statement("UPDATE content_api_codes set cp_action_type = 2");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('content_api_codes', function(Blueprint $table)
		{
			$table->dropColumn('cp_action_type');
		});
	}

}
