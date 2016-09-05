<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacebookMarketingSearchFanHistories extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("facebook_marketing_search_fan_histories", function(Blueprint $t) {
            $t->increments("id");
            $t->unsignedInteger("audience_id");
            $t->text("search_condition");
            $t->boolean("del_flg")->default(false);
            $t->timestamps();
            $t->foreign('audience_id')->references('id')->on('facebook_marketing_audiences');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop("facebook_marketing_search_fan_histories");
	}

}
