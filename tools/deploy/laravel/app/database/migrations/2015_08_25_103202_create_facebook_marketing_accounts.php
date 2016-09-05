<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacebookMarketingAccounts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("facebook_marketing_accounts", function(Blueprint $t){
            $t->increments("id");
            $t->unsignedInteger("marketing_user_id");
            $t->string("account_id", 255)->default("");
            $t->string("account_name", 255)->default("");
            $t->integer("role")->default(0);
            $t->integer("status")->default(0);
            $t->boolean("custom_audience_tos")->default(false);
            $t->boolean("web_custom_audience_tos")->default(false);
            $t->boolean("del_flg")->default(false);
            $t->timestamps();
            $t->foreign('marketing_user_id')->references('id')->on('facebook_marketing_users');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop("facebook_marketing_accounts");
	}

}
