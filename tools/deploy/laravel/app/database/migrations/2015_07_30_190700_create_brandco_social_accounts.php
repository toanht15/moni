<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandcoSocialAccounts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('brandco_social_accounts', function(Blueprint $table)
		{
			$table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->integer('social_app_id');
            $table->string('social_media_account_id', 255);
			$table->longText('access_token');
			$table->string('refresh_token', 255);
			$table->dateTime('token_update_at')->default('0000-00-00 00:00:00');
			$table->text('store');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('brandco_social_accounts');
	}

}
