<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWriteType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('static_html_entries', function(Blueprint $table)
		{
            $table->tinyInteger('write_type')->default(1)->after('title');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('static_html_entries', function(Blueprint $table)
		{
            $table->dropColumn('write_type');
		});
	}

}
