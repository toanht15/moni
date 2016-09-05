<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManager extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('managers', function($t)
		{
			$t->string('mail_address_hash',255)->default('')->after('mail_address');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('managers', function(Blueprint $t)
		{
			$t->dropColumn('mail_address_hash');
		});
	}

}
