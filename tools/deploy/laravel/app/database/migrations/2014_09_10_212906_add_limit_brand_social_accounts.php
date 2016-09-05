<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLimitBrandSocialAccounts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('brand_social_accounts', function (Blueprint $table) {

            $table->integer('display_panel_limit')->default(0)->after('social_app_id');

        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('brand_social_accounts', function (Blueprint $table) {

            $table->dropColumn('display_panel_limit');

        });
	}

}
