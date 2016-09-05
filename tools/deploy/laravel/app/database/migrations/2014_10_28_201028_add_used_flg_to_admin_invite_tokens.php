<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUsedFlgToAdminInviteTokens extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('admin_invite_tokens', function(Blueprint $table)
		{
            $table->tinyInteger('used_flg')->default(0)->after('password');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('admin_invite_tokens', function(Blueprint $table)
		{
            $table->dropColumn('used_flg');
		});
	}

}
