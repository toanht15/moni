<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLinkEntryTarget extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('link_entries', function(Blueprint $t)
        {
            $t->tinyInteger('target')->default(0)->after('link');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('link_entries', function(Blueprint $t)
        {
            $t->dropColumn('target');
        });
	}

}
