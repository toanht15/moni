<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddHeaderTagAndCustomMailToBrandPageSettings extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('brand_page_settings', function(Blueprint $table)
        {
            $table->longText('header_tag_text')->after('tag_text');
            $table->tinyInteger('send_signup_mail_flg')->default(0)->after('rtoaster');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('brand_page_settings', function(Blueprint $table)
        {
            $table->dropColumn('header_tag_text');
            $table->dropColumn('send_signup_mail_flg');
        });
	}
}
