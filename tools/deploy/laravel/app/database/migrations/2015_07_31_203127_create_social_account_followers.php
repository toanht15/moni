<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialAccountFollowers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('social_account_followers', function(Blueprint $table)
		{
            $table->increments('id');
            $table->unsignedInteger('brand_social_account_id');
            $table->integer('value');
            $table->boolean('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('brand_social_account_id')->references('id')->on('brand_social_accounts');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('social_account_followers');
	}

}
