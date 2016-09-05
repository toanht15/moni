<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStaticHtmlEntriesEncode extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('static_html_entries', function(Blueprint $table)
		{
            $table->longText('encode_body')->after('body');
            $table->longText('encode_extra_body')->after('extra_body');
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
            $table->dropColumn('encode_body');
            $table->dropColumn('encode_extra_body');
		});
	}

}
