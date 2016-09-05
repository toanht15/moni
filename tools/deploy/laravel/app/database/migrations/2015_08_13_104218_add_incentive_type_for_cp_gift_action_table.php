<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIncentiveTypeForCpGiftActionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cp_gift_actions', function(Blueprint $table)
		{
			$table->tinyInteger('incentive_type')->default(1)->after('card_required');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cp_gift_actions', function(Blueprint $table)
		{
			$table->dropColumn('incentive_type');
		});
	}

}
