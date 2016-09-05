<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdsAccountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('ads_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ads_user_id')->unsigned();
            $table->string('account_id');
            $table->string('account_name');
            $table->string('social_app_id');
            $table->text('extra_data');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('ads_user_id')->references('id')->on('ads_users');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('ads_accounts');
	}

}
