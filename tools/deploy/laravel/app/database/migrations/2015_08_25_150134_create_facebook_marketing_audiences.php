<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacebookMarketingAudiences extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("facebook_marketing_audiences", function(Blueprint $t) {
            $t->increments("id");
            $t->unsignedInteger("account_id");
            $t->string("name", 255)->default("");
            $t->string("description", 1024)->default("");
            $t->integer("operation_status")->default(0);
            $t->string("availability", 255)->default("");
            $t->string("audience_id", 255)->default("");
            $t->text("store");
            $t->boolean("del_flg")->default(false);
            $t->timestamps();
            $t->foreign('account_id')->references('id')->on('facebook_marketing_accounts');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop("facebook_marketing_audiences");
	}

}
