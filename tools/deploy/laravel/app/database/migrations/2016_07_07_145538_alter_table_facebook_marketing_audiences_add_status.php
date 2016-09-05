<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableFacebookMarketingAudiencesAddStatus extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('facebook_marketing_audiences', function(Blueprint $table)
		{
            $table->tinyInteger('status')->default(0)->after('store');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('facebook_marketing_audiences', function(Blueprint $table)
		{
            $table->dropColumn('status');
		});
	}

}
