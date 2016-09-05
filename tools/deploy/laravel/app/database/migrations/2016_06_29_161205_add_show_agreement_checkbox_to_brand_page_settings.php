<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShowAgreementCheckboxToBrandPageSettings extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brand_page_settings', function(Blueprint $table)
		{
			$table->tinyInteger('show_agreement_checkbox')->default(0)->after('agreement');;
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
			$table->dropColumn('show_agreement_checkbox');
		});
	}

}
