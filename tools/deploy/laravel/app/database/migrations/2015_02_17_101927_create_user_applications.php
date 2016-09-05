<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserApplications extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_applications', function(Blueprint $table)
		{
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->tinyInteger('app_id')->default(1);
            $table->string('access_token', 255)->default('');
            $table->string('refresh_token', 255)->default('');
            $table->text('client_id')->default('');
            $table->dateTime('token_update_at');
            $table->boolean('del_flg')->default(0);
            $table->timestamps();
            $table->unique(array('user_id', 'app_id'), 'user_app_unique_key');
            $table->foreign('user_id', 'users_user_id_foreign')->references('id')->on('users');
		});

        DB::statement("INSERT INTO user_applications SELECT null as id, id as user_id, 1 as app_id, mp_access_token as access_token, mp_refresh_token as refresh_token, mp_client_id as client_id, mp_token_update_at as token_update_at, 0 as del_flg, now() as created_at, now() as updated_at FROM users WHERE del_flg = 0;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_applications');
	}

}
