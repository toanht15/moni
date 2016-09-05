<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderBrandcoSocialAccount extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('brand_social_accounts', function(Blueprint $t)
        {
            $t->integer('order_no')->default(0)->after('user_id');
        });

        Schema::table('rss_streams', function(Blueprint $t)
        {
            $t->integer('order_no')->default(0)->after('brand_id');
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
            $t->dropColumn('order_no');
        });

        Schema::table('rss_streams', function(Blueprint $t)
        {
            $t->dropColumn('order_no');
        });

    }

}
