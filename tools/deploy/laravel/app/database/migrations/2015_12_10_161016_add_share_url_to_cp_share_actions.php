<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShareUrlToCpShareActions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_share_actions', function(Blueprint $table)
		{
			$table->string('share_url',512)->after('button_label_text');
            $table->text('meta_data')->after('share_url');
		});
	}

	/**
	 * Reverse the migrations.
     *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_share_actions', function(Blueprint $table)
		{
			$table->dropColumn('share_url');
            $table->dropColumn('meta_data');
		});
	}

}
