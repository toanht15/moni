<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacebookMarketingUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("facebook_marketing_users", function (Blueprint $t) {
            $t->increments("id");
            $t->unsignedBigInteger("brand_user_relation_id");
            $t->string("media_id", 255)->default("");
            $t->string("name", 255)->default("");
            $t->string("image_url", 255)->default("");
            $t->string("access_token", 255)->default("");
            $t->boolean("token_expired_flg")->default(false);
            $t->boolean("del_flg")->default(false);
            $t->timestamps();
            $t->foreign('brand_user_relation_id')->references('id')->on('brands_users_relations');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop("facebook_marketing_users");
	}

}
