<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToEngagementLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('engagement_logs', function(Blueprint $table)
        {
            $table->tinyInteger('status')->default(0)->after('brand_social_account_id');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('engagement_logs', function(Blueprint $table)
        {
            $table->dropColumn('status');
        });
	}

}
