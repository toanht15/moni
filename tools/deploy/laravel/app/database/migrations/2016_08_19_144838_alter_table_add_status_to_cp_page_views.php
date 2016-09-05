<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddStatusToCpPageViews extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_page_views', function (Blueprint $table) {
			$table->tinyInteger('status')->after('user_count')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_page_views', function (Blueprint $table) {
			$table->dropColumn('status');
		});
	}

}
