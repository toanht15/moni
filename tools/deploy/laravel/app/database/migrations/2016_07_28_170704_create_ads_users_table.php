<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdsUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('ads_users', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('brand_user_relation_id')->unsigned();
            $table->string('social_app_id');
            $table->string('social_account_id');
            $table->string('name');
            $table->string('access_token');
            $table->string('secret_access_token');
            $table->tinyInteger('token_expired_flg')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('brand_user_relation_id')->references('id')->on('brands_users_relations');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('ads_users');
	}
}
