<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdsTargetUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('ads_target_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ads_audiences_accounts_relation_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->string('sns_uid');
            $table->string('email');

            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('ads_audiences_accounts_relation_id')->references('id')->on('ads_audiences_accounts_relations');
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
        Schema::drop('ads_target_users');
	}

}
