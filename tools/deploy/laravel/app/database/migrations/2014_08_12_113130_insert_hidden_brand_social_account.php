<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertHiddenBrandSocialAccount extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('brand_social_accounts', function(Blueprint $table)
        {
            $table->tinyInteger('hidden_flg')->default(0)->after('need_update');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('brand_social_accounts', function(Blueprint $table)
        {
            $table->dropColumn('hidden_flg');
        });
	}

}
