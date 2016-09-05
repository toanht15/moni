<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDisplayPanelLimitRssStream extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('rss_streams', function (Blueprint $table) {

            $table->integer('display_panel_limit')->default(0)->after('link');

        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('rss_streams', function (Blueprint $table) {

            $table->dropColumn('display_panel_limit');

        });
	}

}
