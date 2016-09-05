<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterBrandSocialAccounts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brand_social_accounts', function(Blueprint $t) {
            $t->dropColumn('need_update');
            $t->boolean('token_expired_flg')->after('token_update_at')->default(false);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('brand_social_accounts', function(Blueprint $t) {
            $t->dropColumn('token_expired_flg');
            $t->tinyInteger('need_update')->default(0)->after('store');
        });
	}

}
