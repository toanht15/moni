<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStaticHtmlEntriesJsonBody extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('static_html_entries', function(Blueprint $table)
		{
            // json形式でのエンコードにはしなくなったので、カラム名はenc_bodyに変更
            $table->text('enc_body')->after('body');
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
            $table->dropColumn('enc_body');
		});
	}

}
