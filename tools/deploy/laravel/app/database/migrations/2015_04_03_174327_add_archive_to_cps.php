<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddArchiveToCps extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('cps', function(Blueprint $table)
        {
            $table->boolean('archive_flg')->default(false)->after('extend_tag');
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
            $table->dropColumn('archive_flg');
        });
	}

}
