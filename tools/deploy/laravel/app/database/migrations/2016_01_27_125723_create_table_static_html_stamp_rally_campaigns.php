<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableStaticHtmlStampRallyCampaigns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('static_html_stamp_rally_campaigns', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('static_html_stamp_rally_id');
            $table->unsignedInteger('campaign_id');
            $table->foreign('static_html_stamp_rally_id','stamp_rally_id')->references('id')->on('static_html_stamp_rallies');
            $table->foreign('campaign_id')->references('id')->on('cps');
            $table->timestamps();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('static_html_stamp_rally_campaigns');
	}
}
