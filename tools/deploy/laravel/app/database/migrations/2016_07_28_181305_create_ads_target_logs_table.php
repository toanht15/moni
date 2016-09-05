<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdsTargetLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('ads_target_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ads_audiences_accounts_relation_id')->unsigned();
            $table->integer('total');
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('ads_audiences_accounts_relation_id')->references('id')->on('ads_audiences_accounts_relations');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('ads_target_logs');
	}
}
