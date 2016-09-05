<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFacebookMarketingTargetLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
    {
        Schema::create('facebook_marketing_target_logs', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('audience_id');
            $table->integer('total');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('audience_id')->references('id')->on('facebook_marketing_audiences');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('facebook_marketing_target_logs');
	}
}
