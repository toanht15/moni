<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAttractDefault extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('cps', function(Blueprint $table)
        {
            $table->dropColumn('show_top_page_flg');
            $table->dropColumn('show_navigation_flg');
        });


        Schema::table('cps', function(Blueprint $table)
        {
            $table->tinyInteger('show_top_page_flg')->default(1)->after("show_monipla_com_flg");
            $table->tinyInteger('show_navigation_flg')->default(1)->after("show_top_page_flg");
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
            $table->dropColumn('show_top_page_flg');
            $table->dropColumn('show_navigation_flg');
        });

        Schema::table('cps', function(Blueprint $table)
        {
            $table->tinyInteger('show_top_page_flg')->default(0)->after("show_monipla_com_flg");
            $table->tinyInteger('show_navigation_flg')->default(0)->after("show_top_page_flg");
        });
	}

}
