<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDesignDataToJoinFinishAction extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_join_finish_actions', function(Blueprint $table)
		{
			$table->string('image_url')->after('text')->default('');
			$table->tinyInteger('design_type')->after('cv_tag')->default(1);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_join_finish_actions', function(Blueprint $table)
		{
			$table->dropColumn('image_url');
			$table->dropColumn('design_type');
		});
	}

}
