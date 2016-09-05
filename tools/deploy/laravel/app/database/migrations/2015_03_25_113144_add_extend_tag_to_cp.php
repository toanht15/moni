<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtendTagToCp extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('cps', function(Blueprint $table)
        {
            $table->text('extend_tag')->after('recruitment_note');
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
            $table->dropColumn('extend_tag');
        });
	}

}
