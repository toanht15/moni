<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersSetProvisionalFlg extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function(Blueprint $table)
		{
			DB::statement('UPDATE `users` SET `users`.`provisional_flg` = 0 WHERE NOT EXISTS (SELECT 1 FROM `pre_users` WHERE `pre_users`.`user_id` = `users`.`id`)');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function(Blueprint $table)
		{
			DB::statement('UPDATE `users` SET `users`.`provisional_flg` = 1 WHERE NOT EXISTS (SELECT 1 FROM `pre_users` WHERE `pre_users`.`user_id` = `users`.`id`)');
		});
	}

}
