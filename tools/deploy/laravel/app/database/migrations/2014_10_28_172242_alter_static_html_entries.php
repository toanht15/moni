<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStaticHtmlEntries extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('static_html_entries', function(Blueprint $table)
		{
			//
            $table->string('meta_description', 511)->default('')->after('body');
            $table->string('meta_keyword', 511)->default('')->after('meta_description');
            $table->string('og_image_url', 511)->default('')->after('meta_keyword');
            $table->datetime('public_date')->default('0000-00-00 00:00:00')->after('og_image_url');
            $table->tinyInteger('top_panel_display_flg')->default(0)->after('public_date');
            $table->text('sns_plugin_tag_text')->after('top_panel_display_flg');
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
            $table->dropColumn('meta_description');
            $table->dropColumn('meta_keyword');
            $table->dropColumn('og_image_url');
            $table->dropColumn('public_date');
            $table->dropColumn('top_panel_display_flg');
            $table->dropColumn('sns_plugin_tag_text');
		});
	}

}
