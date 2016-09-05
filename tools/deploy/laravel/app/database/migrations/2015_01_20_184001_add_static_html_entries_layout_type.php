<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStaticHtmlEntriesLayoutType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('static_html_entries', function(Blueprint $table) {
			$table->boolean('title_hidden_flg')->after('hidden_flg')->default(0);
			$table->tinyInteger('layout_type')->after('title_hidden_flg')->default(1);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('static_html_entries', function(Blueprint $table) {
			$table->dropColumn('title_hidden_flg');
			$table->dropColumn('layout_type');
		});
	}

}
