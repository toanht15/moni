<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBrandSocialAccount extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('brand_social_accounts', function($t)
        {
            $t->tinyInteger('need_update')->default(0)->after('store');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('brand_social_accounts', function(Blueprint $t)
        {
            $t->dropColumn('need_update');
        });
	}

}
