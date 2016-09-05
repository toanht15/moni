<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFacebookEntryStatusType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('facebook_entries', function(Blueprint $t)
        {
            $t->string('status_type', 255)->nullable()->after('type');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('facebook_entries', function(Blueprint $t)
        {
            $t->dropColumn('status_type');
        });
	}

}
