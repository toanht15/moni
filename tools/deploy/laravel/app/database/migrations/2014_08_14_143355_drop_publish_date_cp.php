<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropPublishDateCp extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('cps', function(Blueprint $table)
        {
            $table->dropColumn('publish_date');
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
            $table->dateTime('publish_date')->default('0000-00-00 00:00:00')->after('title');
        });
	}

}
