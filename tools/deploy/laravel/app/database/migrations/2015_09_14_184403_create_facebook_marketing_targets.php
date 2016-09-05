<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacebookMarketingTargets extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("facebook_marketing_targets", function(Blueprint $t) {
            $t->increments("id");
            $t->unsignedInteger("audience_id");
            $t->unsignedBigInteger("user_id");
            $t->string("fb_uid", 255)->default("");
            $t->string("email", 255)->default("");
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
		Schema::drop("facebook_marketing_targets");
	}

}
