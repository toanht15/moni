<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStaticHtmlEntriesEncExtraBody extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('static_html_entries', function(Blueprint $table)
		{
            $table->text('enc_extra_body')->after('extra_body');
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
            $table->dropColumn('enc_extra_body');
		});
	}

}
