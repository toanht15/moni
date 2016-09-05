<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteShowNavigationFlgFromCps extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cps', function(Blueprint $t) {
            $t->dropColumn("show_navigation_flg");
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('cps', function(Blueprint $t) {
            $t->boolean("show_navigation_flg")->after("show_top_page_flg")->default(true);
        });
	}

}
