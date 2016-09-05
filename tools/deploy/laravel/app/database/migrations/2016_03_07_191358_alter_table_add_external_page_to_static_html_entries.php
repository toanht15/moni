<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddExternalPageToStaticHtmlEntries extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('static_html_entries', function(Blueprint $table)
        {
            $table->tinyInteger('embed_flg')->after('priority_flg')->default(0);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('static_html_entries', function(Blueprint $table)
        {
            $table->dropColumn('embed_flg');
        });
	}
}
