<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSendMailFlgToCps extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('cps', function(Blueprint $table)
        {
            $table->boolean('send_mail_flg')->default(false)->after('show_navigation_flg');
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
            $table->dropColumn('send_mail_flg');
        });
	}

}
