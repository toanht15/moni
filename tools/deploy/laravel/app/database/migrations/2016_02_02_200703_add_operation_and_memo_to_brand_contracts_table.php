<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOperationAndMemoToBrandContractsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brand_contracts', function(Blueprint $table)
		{
            $table->tinyInteger('operation')->comment('運用主体')->default(0)->after('plan');
            $table->longText('memo')->after('delete_status');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('brand_contracts', function(Blueprint $table)
		{
            $table->dropColumn('operation');
            $table->dropColumn('memo');
		});
	}

}
