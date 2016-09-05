<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCpsAnnounceLabel extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cps', function(Blueprint $table)
		{
            $table->tinyInteger('announce_display_label_use_flg')->default(0)->after('winner_label');
            $table->string('announce_display_label', 255)->default('')->after('announce_display_label_use_flg');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cps', function(Blueprint $table)
		{
            $table->dropColumn('announce_display_label_use_flg');
            $table->dropColumn('announce_display_label');
		});
	}

}
