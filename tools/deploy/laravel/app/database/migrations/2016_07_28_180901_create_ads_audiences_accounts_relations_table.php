<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdsAudiencesAccountsRelationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('ads_audiences_accounts_relations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ads_audience_id')->unsigned();
            $table->integer('ads_account_id')->unsigned();
            $table->string('sns_audience_id');
            $table->text('extra_data');
            $table->tinyInteger('type')->default(0);
            $table->tinyInteger('auto_send_target_flg')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('ads_audience_id')->references('id')->on('ads_audiences');
            $table->foreign('ads_account_id')->references('id')->on('ads_accounts');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('ads_audiences_accounts_relations');
	}
}
